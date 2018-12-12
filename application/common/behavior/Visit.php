<?php
/**
 *
 * 访问记录 - 行为
 *
 * @package   NiPHP
 * @category  application\common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class Visit
{
    /**
     * 访问记录
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        $this->addedVisit();
        $this->addedSearchengine();
        $this->createSitemap();
    }

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        # code...
    }

    /**
     * 记录访问日志
     * @access public
     * @param
     * @return void
     */
    public function addedVisit()
    {
        $key = $this->isSpider();
        if ($key !== false) {
            return false;
        }

        $ip_info = logic('common/IpInfo')->getInfo();
        if (in_array($ip_info['ip'], ['127.0.0.1'])) {
            return false;
        }

        $user_agent = safe_filter_strict(request()->server('HTTP_USER_AGENT'));

        $result =
        model('common/model/visit')
        ->field(true)
        ->where([
            ['ip', '=', $ip_info['ip']],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache(__METHOD__ . md5($ip_info['ip'] . $user_agent))
        ->value('ip');

        if ($result) {
            model('common/model/visit')
            ->where([
                ['ip', '=', $ip_info['ip']],
                ['user_agent', '=', $user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/model/visit')
            ->added([
                'ip'         => $ip_info['ip'],
                'ip_attr'    => $ip_info['country'] . $ip_info['region'] .
                                $ip_info['city'] . $ip_info['area'],
                'user_agent' => $user_agent,
                'date'       => strtotime(date('Y-m-d'))
            ]);
        }

        $this->remove('visit');

        // trace('[behavior] visit', 'warning');
    }

    public function createSitemap()
    {
        $file = env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'sitemap.xml';
        if (is_file($file) && filectime($file) >= strtotime('-1 days')) {
            return false;
        } elseif (is_file($file)) {
            @unlink($file);
        }

        global $map, $field;
        $field = ['id', 'category_id', 'title', 'show_time', 'update_time', 'create_time'];
        $map = [
            ['is_pass', '=', 1],
            ['show_time', '<=', time()]
        ];

        $result =
        db()->field($field)
        ->table('np_article')
        ->union(function($query){
            global $map, $field;
            $query->field($field)
            ->table('np_download')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field;
            $query->field($field)
            ->table('np_picture')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field;
            $query->field($field)
            ->table('np_product')
            ->where($map);
        })
        ->where($map)
        ->order('show_time DESC, update_time DESC')
        ->limit(100)
        ->select();

        foreach ($result as $key => $value) {
            // $result[$key]['flag'] = encrypt($value['id']);
            // $result[$key]['title'] = htmlspecialchars_decode($value['title']);
            $result[$key]['cat_url'] = url('list/' . $value['category_id'], '', true);

            // 查询模型表名
            $table_name =
            model('common/category')
            ->view('category c', ['model_id'])
            ->view('model m', ['table_name'], 'm.id=c.model_id')
            ->where([
                ['c.id', '=', $value['category_id']],
            ])
            ->cache(__METHOD__ . 'TABLE_NAME' . $value['category_id'])
            ->value('table_name');

            $result[$key]['url'] = url($table_name . '/' . $value['category_id'] . '/' . $value['id'], '', true);
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL .
                '<url>' . PHP_EOL .
                '<loc>' . request()->root(true) . '</loc>' . PHP_EOL .
                '<lastmod>' . date('Y-m-d H:i:s') . '</lastmod>' . PHP_EOL .
                '<changefreq>daily</changefreq>' . PHP_EOL .
                '<priority>1.00</priority>' . PHP_EOL .
                '</url>' . PHP_EOL;

        $cat_url = '';
        foreach ($result as $key => $value) {
            $value['update_time'] = $value['update_time'] ? date('Y-m-d', $value['update_time']) : date('Y-m-d');

            if ($cat_url != $value['cat_url']) {
                $xml .= '<url>' . PHP_EOL .
                        '<loc>' . $value['cat_url'] . '</loc>' . PHP_EOL .
                        '<lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL .
                        '<changefreq>daily</changefreq>' . PHP_EOL .
                        '<priority>1.0</priority>' . PHP_EOL .
                        '</url>' . PHP_EOL;
                $cat_url = $value['cat_url'];
            }

            $xml .= '<url>' . PHP_EOL .
                    '<loc>' . $value['url'] . '</loc>' . PHP_EOL .
                    '<lastmod>' . $value['update_time'] . '</lastmod>' . PHP_EOL .
                    '<changefreq>weekly</changefreq>' . PHP_EOL .
                    '<priority>0.8</priority>' . PHP_EOL .
                    '</url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        file_put_contents($file, $xml);
    }

    /**
     * 记录搜索蜘蛛访问日志
     * @access public
     * @param
     * @return void
     */
    public function addedSearchengine()
    {
        $key = $this->isSpider();
        if ($key === false) {
            return false;
        }

        $user_agent = safe_filter_strict(request()->server('HTTP_USER_AGENT'));

        $result =
        model('common/model/searchengine')
        ->field(true)
        ->where([
            ['name', '=', $key],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache(__METHOD__ . md5($ip_info['ip'] . $user_agent))
        ->value('name');

        if ($result) {
            model('common/model/searchengine')
            ->where([
                ['name', '=', $key],
                ['user_agent', '=', $user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/model/searchengine')
            ->added([
                'name'       => $key,
                'user_agent' => $user_agent,
                'date'       => strtotime(date('Y-m-d'))
            ]);
        }

        $this->remove('searchengine');

        // trace('[behavior] searchengine', 'warning');
    }

    /**
     * 删除过期的搜索日志(保留三个月)
     * @access protected
     * @param
     * @return void
     */
    protected function remove($_model_name)
    {
        if (rand(0, 10) !== 0) {
            return false;
        }

        model('common/model/' . $_model_name)
        ->where([
            ['date', '<=', strtotime('-90 days')]
        ])
        ->limit(1000)
        ->delete();
    }

    /**
     * 判断搜索引擎蜘蛛
     * @access protected
     * @param
     * @return mixed
     */
    protected function isSpider()
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

        $user_agent = strtolower(request()->server('HTTP_USER_AGENT'));
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }
}
