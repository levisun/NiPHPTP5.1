<?php
/**
 *
 * 服务层
 * 网站信息
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

use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\model\Config as ModelConfig;
use app\model\Article as ModelArticle;
use app\model\Category as ModelCategory;

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

        // 文章描述
        if ($id = Request::param('id/f', null)) {
            $result =
            ModelArticle::where([
                ['id', '=', $id]
            ])
            ->value('description', '');
        }
        // 栏目描述
        elseif ($cid = Request::param('cid/f', null)) {
            $result =
            ModelCategory::where([
                ['id', '=', $cid]
            ])
            ->value('description', '');
        }
        else {
            $result .=
            ModelConfig::where([
                ['name', '=', Request::controller(true) . '_description'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::controller(true) . '_description' . Lang::detect(), null, 'SITEINFO')
            ->value('value', '');
        }

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

        // 文章关键词
        if ($id = Request::param('id/f', false)) {
            $result =
            ModelArticle::where([
                ['id', '=', $id]
            ])
            ->value('keywords', '');
        }
        // 栏目关键词
        elseif ($cid = Request::param('cid/f', false)) {
            $result =
            ModelCategory::where([
                ['id', '=', $cid]
            ])
            ->value('keywords', '');
        }
        else {
            $result .=
            ModelConfig::where([
                ['name', '=', Request::controller(true) . '_keywords'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::controller(true) . '_keywords' . Lang::detect(), null, 'SITEINFO')
            ->value('value', '');
        }

        return strip_tags($result);
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
            $result =
            ModelArticle::where([
                ['id', '=', $id]
            ])
            ->value('title', '');
        }
        // 栏目名
        elseif ($cid = Request::param('cid/f', false)) {
            $result =
            ModelCategory::where([
                ['id', '=', $cid]
            ])
            ->value('name', 'NICMS');
        }
        else {
            $result .=
            ModelConfig::where([
                ['name', '=', Request::controller(true) . '_sitename'],
                ['lang', '=', Lang::detect()]
            ])
            ->cache(__METHOD__ . Request::controller(true) . '_sitename' . Lang::detect(), null, 'SITEINFO')
            ->value('value', 'NICMS');
        }

        return strip_tags($result);
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
        ModelConfig::where([
            ['name', '=', Request::controller(true) . '_copyright'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_copyright' . Lang::detect(), null, 'SITEINFO')
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
        ModelConfig::where([
            ['name', '=', Request::controller(true) . '_bottom'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_bottom' . Lang::detect(), null, 'SITEINFO')
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
        ModelConfig::where([
            ['name', '=', Request::controller(true) . '_script'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_script' . Lang::detect(), null, 'SITEINFO')
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
        ModelConfig::where([
            ['name', '=', Request::controller(true) . '_theme'],
            ['lang', '=', Lang::detect()]
        ])
        ->cache(__METHOD__ . Request::controller(true) . '_theme' . Lang::detect(), null, 'SITEINFO')
        ->value('value', 'default');
    }
}
