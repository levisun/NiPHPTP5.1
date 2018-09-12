<?php
/**
 *
 * 访问统计 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\expand
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\logic;

class Visit
{

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

        $result =
        model('common/visit')
        ->field(true)
        ->where([
            ['ip', '=', $ip_info['ip']],
            ['user_agent', '=', request()->header('user-agent')],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache(true)
        ->value('ip');

        if ($result) {
            model('common/visit')
            ->where([
                ['ip', '=', $ip_info['ip']],
                ['user_agent', '=', request()->header('user-agent')],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/visit')
            ->added([
                'ip'         => $ip_info['ip'],
                'ip_attr'    => $ip_info['country'] . $ip_info['region'] .
                                $ip_info['city'] . $ip_info['area'],
                'user_agent' => request()->header('user-agent'),
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

        $result =
        model('common/searchengine')
        ->field(true)
        ->where([
            ['name', '=', $key],
            ['user_agent', '=', request()->header('user-agent')],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->value('name');

        if ($result) {
            model('common/searchengine')
            ->where([
                ['name', '=', $key],
                ['user_agent', '=', request()->header('user-agent')],
                ['date', '=', strtotime(date('Y-m-d'))]
            ])
            ->setInc('count');
        } else {
            model('common/searchengine')
            ->added([
                'name'       => $key,
                'user_agent' => request()->header('user-agent'),
                'date'       => strtotime(date('Y-m-d'))
            ]);
        }

        $this->remove('searchengine');
    }

    /**
     * 删除过期的搜索日志(保留三个月)
     * @access public
     * @param
     * @return void
     */
    public function remove($_model_name)
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

        $user_agent = request()->header('user-agent');
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }
}
