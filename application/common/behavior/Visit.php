<?php
/**
 *
 * 访问记录 - 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
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
        if (request_block(['admin', 'member', 'wechat'])) {
            return true;
        }

        $this->addedVisit();
        $this->addedSearchengine();

        // 记录每次访问的地址,方便跳转返回
        if (cookie('?back_url')) {
            $back_url = cookie('back_url');
            $back_url = array_merge($back_url, [request()->url(true)]);
        } else {
            $back_url = [
                request()->url(true)
            ];
        }
        $back_url = array_slice($back_url, -3, 3);
        cookie('back_url', $back_url);
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
     * 写入访问日志
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

        $user_agent = safe_filter(request()->server('HTTP_USER_AGENT'), true, true);

        $result =
        model('common/visit')
        ->field(true)
        ->where([
            ['ip', '=', $ip_info['ip']],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache('VISIT ADDEDVISIT' . md5($ip_info['ip'] . $user_agent))
        ->value('ip');

        if ($result) {
            model('common/visit')
            ->where([
                ['ip', '=', $ip_info['ip']],
                ['user_agent', '=', $user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/visit')
            ->added([
                'ip'         => $ip_info['ip'],
                'ip_attr'    => $ip_info['country'] . $ip_info['region'] .
                                $ip_info['city'] . $ip_info['area'],
                'user_agent' => $user_agent,
                'date'       => strtotime(date('Y-m-d'))
            ]);
        }

        $this->remove('visit');
    }

    /**
     * 写入搜索日志
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

        $user_agent = safe_filter(request()->server('HTTP_USER_AGENT'), true, true);

        $result =
        model('common/searchengine')
        ->field(true)
        ->where([
            ['name', '=', $key],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache('VISIT ADDEDSEARCHENGINE' . md5($ip_info['ip'] . $user_agent))
        ->value('name');

        if ($result) {
            model('common/searchengine')
            ->where([
                ['name', '=', $key],
                ['user_agent', '=', $user_agent],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/searchengine')
            ->added([
                'name'       => $key,
                'user_agent' => $user_agent,
                'date'       => strtotime(date('Y-m-d'))
            ]);
        }

        $this->remove('searchengine');
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

        model('common/' . $_model_name)
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

        $user_agent = request()->server('HTTP_USER_AGENT');
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }
}
