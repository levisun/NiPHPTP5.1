<?php
/**
 *
 * 访问日志 - 服务层
 *
 * @package   NiPHP
 * @category  app\common\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\server;

use think\facade\Request;

class Accesslog
{
    private $user_agent = '';





    /**
     * 判断搜索引擎蜘蛛
     * @access private
     * @param
     * @return mixed
     */
    private function isSpider()
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

        $user_agent = strtolower(Request::server('HTTP_USER_AGENT'));
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }
}
