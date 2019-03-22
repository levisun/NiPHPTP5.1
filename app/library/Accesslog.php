<?php
/**
 *
 * 服务层
 * 访问日志
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\App;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\library\Ip;
use app\model\Article as ModelArticle;
use app\model\Searchengine as ModelSearchengine;
use app\model\Visit as ModelVisit;

class Accesslog
{
    private $user_agent;
    private $ip;

    public function handle($event, App $app): void
    {
        if (Request::isGet() && !in_array(Request::subDomain(), ['admin', 'api', 'cdn'])) {
            $this->record();
        }
    }

    /**
     * 记录访问
     * @access public
     * @param
     * @return void
     */
    public function record(): void
    {
        $this->user_agent = Request::server('HTTP_USER_AGENT');
        $this->ip = Ip::info();

        // 蜘蛛
        if ($spider = $this->isSpider()) {
            $has =
            ModelSearchengine::where([
                ['name', '=', $spider],
                ['user_agent', '=', $this->user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->cache(__METHOD__ . md5($spider . $this->user_agent))
            ->value('name');

            if ($has) {
                ModelSearchengine::where([
                    ['name', '=', $spider],
                    ['user_agent', '=', $this->user_agent],
                    ['date', '=', strtotime(date('Y-m-d'))]
                ])
                ->inc('count')
                ->update();
            } else {
                ModelSearchengine::insert([
                    'name'       => $spider,
                    'user_agent' => $this->user_agent,
                    'date'       => strtotime(date('Y-m-d'))
                ]);
            }
        }

        // 访问
        else {
            $has =
            ModelVisit::where([
                ['ip', '=', $this->ip['ip']],
                ['user_agent', '=', $this->user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->cache(__METHOD__ . md5($this->ip['ip'] . $this->user_agent))
            ->value('ip');

            if ($has) {
                ModelVisit::where([
                    ['ip', '=', $this->ip['ip']],
                    ['user_agent', '=', $this->user_agent],
                    ['date', '=', strtotime(date('Y-m-d'))]
                ])
                ->inc('count')
                ->update();
            } else {
                ModelVisit::insert([
                    'ip'         => $this->ip['ip'],
                    'ip_attr'    => $this->ip['country'] .  $this->ip['region'] . $this->ip['city'] .  $this->ip['area'],
                    'user_agent' => $this->user_agent,
                    'date'       => strtotime(date('Y-m-d'))
                ]);
            }
        }

        // 删除过期信息
        if (rand(0, 100) === 0) {
            ModelSearchengine::where([
                ['date', '<=', strtotime('-90 days')]
            ])
            ->limit(100)
            ->delete();

            ModelVisit::where([
                ['date', '<=', strtotime('-90 days')]
            ])
            ->limit(100)
            ->delete();
        }

        $this->sitemap();
    }


    /**
     * 判断搜索引擎蜘蛛
     * @access public
     * @param
     * @return mixed
     */
    public function isSpider()
    {
        $searchengine = [
            'GOOGLE'         => 'googlebot',
            'GOOGLE ADSENSE' => 'mediapartners-google',
            'BAIDU'          => 'baiduspider',
            'MSN'            => 'msnbot',
            'YODAO'          => 'yodaobot',
            'YAHOO'          => 'yahoo! slurp;',
            'Yahoo China'    => 'yahoo! slurp china;',
            'IASK'           => 'iaskspider',
            'SOGOU'          => 'sogou web spider',
            'SOGOU'          => 'sogou push spider',
            'YISOU'          => 'yisouspider',
        ];
        $this->user_agent = $this->user_agent ? $this->user_agent : Request::server('HTTP_USER_AGENT');

        $user_agent = strtolower($this->user_agent);
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }

    /**
     * 生成网站地图
     * @access private
     * @param
     * @return boolean
     */
    private function sitemap(): bool
    {
        $path = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . 'sitemap.xml';
        clearstatcache();
        if (is_file($path) && filemtime($path) >= strtotime('-1 days')) {
            return false;
        }

        // 第一次生成查询10万条数据,其后每次更新查询100条数据
        $limit = is_file($path) ? 100 : 10000;

        $xml =  '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL .
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL .
                '<url>' . PHP_EOL .
                '<loc>' . Request::scheme() . '://www.' . Request::rootDomain() . '</loc>' . PHP_EOL .
                '<lastmod>' . date('Y-m-d H:i:s') . '</lastmod>' . PHP_EOL .
                '<changefreq>daily</changefreq>' . PHP_EOL .
                '<priority>1.00</priority>' . PHP_EOL .
                '</url>' . PHP_EOL;

        $article =
        ModelArticle::view('article article', ['id', 'category_id', 'update_time'])
        ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
        ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id and model.id=1')
        ->where([
            ['article.is_pass', '=', '1'],
            ['article.show_time', '<=', time()],
            ['article.lang', '=', Lang::detect()]
        ])
        ->order('article.id DESC')
        ->limit($limit)
        ->select()
        ->toArray();

        $cat_url = '';
        foreach ($article as $key => $value) {
            $value['cat_url'] = Request::scheme() . '://www.' . Request::rootDomain() . '/list/' .
                                $value['action_name'] . '/' . $value['category_id'] . '.html';

            if ($cat_url !== $value['cat_url']) {
                $xml .= '<url>' . PHP_EOL .
                        '<loc>' . $value['cat_url'] . '</loc>' . PHP_EOL .
                        '<lastmod>' . date('Y-m-d H:i:s', $value['update_time']) . '</lastmod>' . PHP_EOL .
                        '<changefreq>daily</changefreq>' . PHP_EOL .
                        '<priority>1.0</priority>' . PHP_EOL .
                        '</url>' . PHP_EOL;
                $cat_url = $value['cat_url'];
            }

            $value['url'] = Request::scheme() . '://www.' . Request::rootDomain() . '/details/' .
                            $value['action_name'] . '/' . $value['category_id'] . '/' .
                            $value['id'] . '.html';

            $xml .= '<url>' . PHP_EOL .
                    '<loc>' . $value['url'] . '</loc>' . PHP_EOL .
                    '<lastmod>' . date('Y-m-d H:i:s', $value['update_time']) . '</lastmod>' . PHP_EOL .
                    '<changefreq>weekly</changefreq>' . PHP_EOL .
                    '<priority>0.8</priority>' . PHP_EOL .
                    '</url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        file_put_contents($path, $xml);

        return true;
    }
}
