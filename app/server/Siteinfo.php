<?php
/**
 *
 * 网站信息 - 方法库
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
            ['name', '=', Request::app() . '_description'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_description')
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
            ['name', '=', Request::app() . '_keywords'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_keywords')
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
            ['name', '=', Request::app() . '_sitename'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_sitename')
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
            ['name', '=', Request::app() . '_copyright'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_copyright')
        ->value('value', 'Copyright &copy; 2013-' . date('Y') . ' <a href="http://www.NiPHP.com" target="_blank" rel="nofollow">失眠小枕头</a>, All rights reserved');

        return htmlspecialchars_decode($result);
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
            ['name', '=', Request::app() . '_bottom'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_bottom')
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
            ['name', '=', Request::app() . '_script'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_script')
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
            ['name', '=', Request::app() . '_theme'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_theme')
        ->value('value', 'default');
    }
}
