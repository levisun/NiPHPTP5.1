<?php
/**
 *
 * 服务层
 * 访问日志
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\App;
use think\facade\Lang;
use think\facade\Log;
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
            Log::record('[ACCESSLOG] 访问记录', 'alert');
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
                ->inc('count', 1, 60)
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
                ->inc('count', 1, 60)
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
}
