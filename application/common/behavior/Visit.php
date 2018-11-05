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
        // 阻挡Ajax Pjax Post类型请求
        // 阻挡common模块请求
        if (request_block() || request()->module() === 'admin') {
            return true;
        }

        $this->addedVisit();
        $this->addedSearchengine();
        // $this->createSitemap();
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

        $ip_info = logic('common/logic/IpInfo')->getInfo();
        if (in_array($ip_info['ip'], ['127.0.0.1'])) {
            return false;
        }

        $user_agent = safe_filter(request()->server('HTTP_USER_AGENT'), true, true);

        $result =
        model('common/model/visit')
        ->field(true)
        ->where([
            ['ip', '=', $ip_info['ip']],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache('VISIT ADDEDVISIT' . md5($ip_info['ip'] . $user_agent))
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
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>
                    <urlset>
                        <url>
                            <loc>' . request()->root(true) . '</loc>
                            <priority>1.00</priority>
                            <lastmod>' . date('Y-m-d H:i:s') . '</lastmod>
                            <changefreq>weekly</changefreq>
                        </url>';

        $result =
        db()->field(['id', 'category_id', 'update_time', 'create_time'])
        ->table('np_article')
        ->union(function($query){
            $query->field(['id', 'category_id', 'update_time', 'create_time'])
            ->table('np_picture')
            ->where([
                ['is_pass', '=', 1],
                ['show_time', '<=', time()]
            ]);
        })
        ->union(function($query){
            $query->field(['id', 'category_id', 'update_time', 'create_time'])
            ->table('np_download')
            ->where([
                ['is_pass', '=', 1],
                ['show_time', '<=', time()]
            ]);
        })
        ->union(function($query){
            $query->field(['id', 'category_id', 'update_time', 'create_time'])
            ->table('np_product')
            ->where([
                ['is_pass', '=', 1],
                ['show_time', '<=', time()]
            ]);
        })
        ->select();
        print_r($result);die();

        $map = [
            ['a.is_pass', '=', 1],
            ['a.show_time', '<=', time()]
        ];

        $result =
        model('common/article')
        ->view('article a', ['id', 'category_id', 'update_time', 'create_time'])
        ->view('category c', ['name' => 'category_name'], 'c.id=a.category_id')
        ->view('model m', ['name' => 'model_tablename'], 'm.id=c.model_id')
        ->where($map)
        ->order('id DESC')
        ->select();
        foreach ($result as $key => $value) {
            $url = url($value->model_tablename . '/' . $value->category_id . '/' . $value->id);
            $url = str_replace('/index/', '/', $url);

            if ($value->update_time) {
                $lastmod = $value->update_time;
            } elseif ($value->create_time) {
                $lastmod = date('Y-m-d H:i:s', $value->create_time);
            } else {
                $lastmod = date('Y-m-d H:i:s', time());
            }

            $xml .= '<url>
                        <loc>' . $url . '</loc>
                        <priority>0.50</priority>
                        <lastmod>' . $lastmod . '</lastmod>
                        <changefreq>weekly</changefreq>
                    </url>';
        }



        $xml .= '</urlset>';

        print_r($xml);die();
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

        $user_agent = safe_filter(request()->server('HTTP_USER_AGENT'), true, true);

        $result =
        model('common/model/searchengine')
        ->field(true)
        ->where([
            ['name', '=', $key],
            ['user_agent', '=', $user_agent],
            ['date', '=', strtotime(date('Y-m-d'))]
        ])
        ->cache('VISIT ADDEDSEARCHENGINE' . md5($ip_info['ip'] . $user_agent))
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
