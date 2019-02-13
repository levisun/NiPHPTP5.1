<?php
/**
 *
 * 网站信息 - 方法库
 *
 * @package   NiPHP
 * @category  app\common\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\library;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\common\model\Config as MConfig;

class Siteinfo
{



    public static function description()
    {
        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        elseif ($cid = Request::param('cid/f', false)) {
            # code...
        }
        else {
            $result =
            MConfig::where([
                ['name', '=', Request::app() . '_description'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::app() . '_description')
            ->value('value', 'description');
        }

        return $result;
    }

    public static function keywords()
    {
        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        elseif ($cid = Request::param('cid/f', false)) {
            # code...
        }
        else {
            $result =
            MConfig::where([
                ['name', '=', Request::app() . '_keywords'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::app() . '_keywords')
            ->value('value', 'keywords');
        }

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
        // 文章名
        if ($id = Request::param('id/f', false)) {
            # code...
        }
        // 栏目名
        elseif ($cid = Request::param('cid/f', false)) {
            # code...
        }
        else {
            $result =
            MConfig::where([
                ['name', '=', Request::app() . '_sitename'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::app() . '_sitename')
            ->value('value', 'title');
        }

        return $result;
    }

    public static function copyright(): string
    {
        $result =
        MConfig::where([
            ['name', '=', Request::app() . '_copyright'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::app() . '_copyright')
        ->value('value', 'copyright');

        return htmlspecialchars_decode($result);
    }

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
        ->value('value', false);

        return $result ? htmlspecialchars_decode($result) : '';
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
