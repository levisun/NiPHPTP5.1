<?php
/**
 *
 * 标签
 *
 * @package   NiPHP
 * @category  application\cms\taglib
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\taglib;

use think\template\TagLib;

class Label extends TagLib
{
    // 标签定义
    // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    protected $tags = [
        'tags'    => ['close' => 1, 'attr' => '', 'alias' => 'tag'],
        'search'  => ['close' => 1, 'attr' => '', 'alias' => 'search'],
        'article' => ['close' => 1, 'attr' => '', 'alias' => 'page'],
        'catlist' => ['close' => 1, 'attr' => '', 'alias' => 'list'],
        'nav'     => ['close' => 1, 'attr' => 'type', 'alias' => 'category'],
        'bread'   => ['close' => 1, 'attr' => '', 'alias' => 'breadcrumb'],
        'menu'    => ['close' => 0, 'attr' => '', 'alias' => 'sidebar'],
        'ads'     => ['close' => 1, 'attr' => 'id', 'alias' => 'adv'],
        'banner'  => ['close' => 1, 'attr' => 'id', 'alias' => 'slide'],
        'query'   => ['close' => 1, 'attr' => 'sql', 'alias' => 'db'],
    ];

    /**
     * 标签解析
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagQuery($_tag, $_content)
    {
        /*$parseStr  = '<?php $data = Db::query(' . $_tag['sql'] . ')';

        $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';*/

        // return $parseStr;
    }

    /**
     * 标签
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagTags($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "tags.query",
                            token: "{$Think.const.API_TOKEN}",
                            sign:   jQuery.sign({
                                method: "tags.query",
                                token: "{$Think.const.API_TOKEN}"
                            })
                        },
                        success: function(result){
                            if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                for (var key in data) {
                                    var vo = data[key];
                                    ' . $_content . '
                                }
                            }
                        }
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $tags = logic("cms/tags")->query(); ?>';
            $parseStr .= 'if (is_null($tags)) {abort(404);}';
            $parseStr .= '$count = count($tags);';
            $parseStr .= 'foreach ($tags as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($tags, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }

    public function tagSearch($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        if ($_tag['async'] == 'true') {
            $_tag['q'] = !empty($_tag['q']) ? $_tag['q'] : '{:input("param.q/")}';
            $_tag['p']  = '{:input("param.p/f", 1)}';

            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "search.query",
                            token:  "{$Think.const.API_TOKEN}",
                            q:      "' . $_tag['q'] . '",
                            p:      "' . $_tag['p'] . '",
                            sign:  jQuery.sign({
                                method: "search.query",
                                token:  "{$Think.const.API_TOKEN}",
                                q:      "' . $_tag['q'] . '",
                                p:      "' . $_tag['p'] . '"
                            })
                        },
                        success: function(result){
                            if (result.code === "404") {
                                jQuery.redirect("' . url('error/404') . '");
                            } else if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                ' . $_content . '
                            }
                        }
                    });
                });
                </script>';
        } else {

        }

        return $parseStr;
    }

    /**
     * 文章内容
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagArticle($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input("param.cid/f")}';
            $_tag['id']  = !empty($_tag['id'])  ? (float) $_tag['id'] : '{:input("param.id/f")}';

            $time = time();
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            token: "{$Think.const.API_TOKEN}",
                            timestamp: "' . $time . '",
                            cid:       "' . $_tag['cid'] . '",
                            id:        "' . $_tag['id'] . '",
                            sign:      jQuery.sign({
                                method:    "article.hits",
                                token: "{$Think.const.API_TOKEN}",
                                timestamp: "' . $time . '",
                                cid:       "' . $_tag['cid'] . '",
                                id:        "' . $_tag['id'] . '"
                            })
                        }
                    });
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.query",
                            token: "{$Think.const.API_TOKEN}",
                            cid:   "' . $_tag['cid'] . '",
                            id:    "' . $_tag['id'] . '",
                            sign:  jQuery.sign({
                                method: "article.query",
                                token: "{$Think.const.API_TOKEN}",
                                cid:    "' . $_tag['cid'] . '",
                                id:     "' . $_tag['id'] . '"
                            })
                        },
                        success: function(result){
                            if (result.code === "404") {
                                jQuery.redirect("' . url('error/404') . '");
                            } else if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                jQuery("title").text(data.title+" - "+jQuery("title").text());
                                if (data.keywords) {
                                    jQuery("meta[name=\'keywords\']").attr("content", data.keywords);
                                }
                                if (data.description) {
                                    jQuery("meta[name=\'description\']").attr("content", data.description);
                                }
                                ' . $_content . '
                            }
                        }
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input("param.cid/f")';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : 'input("param.id/f")';

            $parseStr  = '<?php $article = logic("cms/article")->query(' . $_tag['cid'] . ', ' . $_tag['id'] . ');';
            $parseStr .= 'if (is_null($article)) {abort(404);} ?>';
            $parseStr .= $_content;
            $time = time();
            $parseStr .= '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            token: "{$Think.const.API_TOKEN}",
                            timestamp: "' . $time . '",
                            cid:       "' . $_tag['cid'] . '",
                            id:        "' . $_tag['id'] . '",
                            sign:      jQuery.sign({
                                method:    "article.hits",
                                token: "{$Think.const.API_TOKEN}",
                                timestamp: "' . $time . '",
                                cid:       "' . $_tag['cid'] . '",
                                id:        "' . $_tag['id'] . '"
                            })
                        }
                    });
                });
                </script>';
        }

        return $parseStr;
    }

    /**
     * 文章列表
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagCatlist($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input("param.cid/f")}';
            $_tag['p']  = '{:input("param.p/f", 1)}';
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "catlist.query",
                            token:  "{$Think.const.API_TOKEN}",
                            cid:    "' . $_tag['cid'] . '",
                            p:      "' . $_tag['p'] . '",
                            sign:   jQuery.sign({
                                method: "catlist.query",
                                token:  "{$Think.const.API_TOKEN}",
                                cid:    "' . $_tag['cid'] . '",
                                p:      "' . $_tag['p'] . '",
                            })
                        },
                        success: function(result){
                            if (result.code === "404") {
                                jQuery.redirect("' . url('error/404') . '");
                            } else if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                var list = result.data.list;
                                var page = result.data.page;
                                var total = result.data.total;
                                var current_page = result.data.current_page;
                                var last_page = result.data.last_page;
                                var per_page = result.data.per_page;
                                ' . $_content . '
                            }
                        }
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input("param.cid/f")';
            $parseStr  = '<?php $list = logic("cms/catlist")->query(' . $_tag['cid'] . ');';
            $parseStr .= 'if (is_null($list)) {abort(404);}';
            $parseStr .= '$count = count($list["list"]);';
            $parseStr .= 'foreach ($list["list"] as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($list, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }

    /**
     * 幻灯片
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagBanner($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method:   "banner.query",
                            token:    "{$Think.const.API_TOKEN}",
                            slide_id: "' . $_tag['id'] . '",
                            sign:     jQuery.sign({
                                method:   "banner.query",
                                token:    "{$Think.const.API_TOKEN}",
                                slide_id: "' . $_tag['id'] . '"
                            })
                        },
                        success: function(result){
                            if (result.code === "404") {
                                jQuery.redirect("' . url('error/404') . '");
                            } else if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                for (var key in data) {
                                    var vo = data[key];
                                    ' . $_content . '
                                }
                            }
                        }
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $banner = logic("cms/banner")->query(' . $_tag['id'] . ');';
            $parseStr .= 'if (is_null($banner)) {abort(404);}';
            $parseStr .= '$count = count($banner);';
            $parseStr .= 'foreach ($banner as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($banner, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }

    /**
     * 广告
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagAds($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    jQuery.pjax({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method:  "ads.query",
                            token:   "{$Think.const.API_TOKEN}",
                            ads_id:  "' . $_tag['id'] . '",
                            sign:    jQuery.sign({
                                method: "ads.query",
                                token:  "{$Think.const.API_TOKEN}",
                                ads_id: "' . $_tag['id'] . '"
                            })
                        },
                        success: function(result){
                            if (result.code !== "SUCCESS") {
                                return false;
                            }
                            if (result.data) {
                                var data = result.data;
                                for (var key in data) {
                                    var vo = data[key];
                                    ' . $_content . '
                                }
                            }
                        }
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $ads = logic("cms/ads")->query(' . $_tag['id'] . ');';
            $parseStr .= 'if (is_null($ads)) {abort(404);}?>';
            $parseStr .= $_content;
            $parseStr .= '<?php unset($ads); ?>';
        }

        return $parseStr;
    }

    /**
     * 侧导航
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagMenu($_tag, $_content)
    {
        $parseStr  = '<?php $menu = logic("cms/sidebar")->query();';
        $parseStr .= 'if (is_null($menu)) {abort(404);} ?>';
        $parseStr .= $_content;

        return $parseStr;
    }

    /**
     * 面包屑
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagBread($_tag, $_content)
    {
        $parseStr  = '<?php $bread = logic("cms/breadcrumb")->query();';
        $parseStr .= 'if (is_null($bread)) {abort(404);}';
        $parseStr .= '$count = count($bread);';
        $parseStr .= 'foreach ($bread as $key => $vo) { ?>';
        $parseStr .= $_content;
        $parseStr .= '<?php } unset($bread, $count, $key, $vo); ?>';

        return $parseStr;
    }

    /**
     * nav标签解析
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagNav($_tag, $_content)
    {
        $_tag['type']  = !empty($_tag['type']) ? (float) $_tag['type'] : '2';

        $parseStr  = '<?php $nav = logic("cms/nav")->query(' . $_tag['type'] . ');';
        $parseStr .= 'if (is_null($nav)) {abort(404);}';
        $parseStr .= '$count = count($nav);';
        $parseStr .= 'foreach ($nav as $key => $vo) { ?>';
        $parseStr .= $_content;
        $parseStr .= '<?php } unset($nav, $count, $key, $vo); ?>';

        return $parseStr;
    }
}
