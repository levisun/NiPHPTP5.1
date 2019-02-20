<?php
/**
 *
 * 服务层
 * 网站信息
 *
 * @package   NiPHP
 * @category  app\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\model\Config as MConfig;

class Siteinfo
{


    /**
     * 网站描述
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function description(): string
    {
        $result = '';

        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        elseif ($cid = Request::param('cid/f', false)) {
            # code...
        }

        $result .=
        MConfig::where([
            ['name', '=', Request::controller(true) . '_description'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_description', null, 'SITEINFO')
        ->value('value', '');

        return $result;
    }

    /**
     * 网站关键词
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function keywords(): string
    {
        $result = '';

        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        if ($cid = Request::param('cid/f', false)) {
            # code...
        }

        $result .=
        MConfig::where([
            ['name', '=', Request::controller(true) . '_keywords'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_keywords', null, 'SITEINFO')
        ->value('value', '');

        return $result;
    }

    /**
     * 网站标题
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function title(): string
    {
        $result = '';

        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        if ($cid = Request::param('cid/f', false)) {
            # code...
        }

        $result .=
        MConfig::where([
            ['name', '=', Request::controller(true) . '_sitename'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_sitename', null, 'SITEINFO')
        ->value('value', 'NIPHP CMS');

        return $result;
    }

    /**
     * 网站版权
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function copyright(): string
    {
        $result =
        MConfig::where([
            ['name', '=', Request::controller(true) . '_copyright'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_copyright', null, 'SITEINFO')
        ->value('value', '');

        return htmlspecialchars_decode($result) . '<p>Powered by <a href="http://www.niphp.com" target="_blank" rel="nofollow">NiPHP</a></p>';
    }

    /**
     * 网站底部
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function bottom(): string
    {
        $result =
        MConfig::where([
            ['name', '=', Request::controller(true) . '_bottom'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_bottom', null, 'SITEINFO')
        ->value('value', 'bottom');

        return htmlspecialchars_decode($result);
    }

    /**
     * JS脚本
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function script(): string
    {
        $result =
        MConfig::where([
            ['name', '=', Request::controller(true) . '_script'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_script', null, 'SITEINFO')
        ->value('value', '');

        return htmlspecialchars_decode($result);
    }

    /**
     * 主题
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function theme(): string
    {
        return
        MConfig::where([
            ['name', '=', Request::controller(true) . '_theme'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_theme', null, 'SITEINFO')
        ->value('value', 'default');
    }
}
