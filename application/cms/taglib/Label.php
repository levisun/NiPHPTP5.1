<?php
/**
 *
 * 标签
 *
 * @package   NiPHPCMS
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
        'tags' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'tag',
        ],
        'article' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'page'
        ],
        'list' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'list'
        ],
        'nav' => [
            'attr'  => 'type',
            'close' => 1,
            'alias' => 'category'
        ],
        'bread' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'breadcrumb'
        ],
        'menu' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'sidebar'
        ],
        'ads' => [
            'attr'  => 'id',
            'close' => 1,
            'alias' => 'ad'
        ],
        'banner' => [
            'attr'  => 'id',
            'close' => 1,
            'alias' => 'slide',
        ],
        'query'  => [
            'attr'  => 'sql',
            'close' => 1,
            'alias' => 'db',
        ],
    ];

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
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "tags.query",
                            sign:   jQuery.sign({
                                method: "tags.query"
                                })
                        }
                    }, function(result){
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $tags = logic(\'cms/tags\')->query(); ?>';
            $parseStr .= 'if (is_null($tags)) {abort(404);}';
            $parseStr .= '$count = count($tags);';
            $parseStr .= 'foreach ($tags as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($tags, $count, $key, $vo); ?>';
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
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input(\'param.cid/f\')}';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '{:input(\'param.id/f\')}';

            $time = time();
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.query",
                            cid:   "' . $_tag['cid'] . '",
                            id:    "' . $_tag['id'] . '",
                            sign:  jQuery.sign({
                                method: "article.query",
                                cid:    "' . $_tag['cid'] . '",
                                id:     "' . $_tag['id'] . '"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
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
                    });
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            timestamp: "' . $time . '",
                            cid:       "' . $_tag['cid'] . '",
                            id:        "' . $_tag['id'] . '",
                            sign:      jQuery.sign({
                                method:    "article.hits",
                                timestamp: "' . $time . '",
                                cid:       "' . $_tag['cid'] . '",
                                id:        "' . $_tag['id'] . '"
                                })
                        }
                    }, function(){
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input(\'param.cid/f\')';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : 'input(\'param.id/f\')';

            $parseStr  = '<?php $article = logic(\'cms/article\')->query(' . $_tag['cid'] . ', ' . $_tag['id'] . ');';
            $parseStr .= 'if (is_null($article)) {abort(404);}?>';
            $parseStr .= $_content;
            $parseStr .= '<?php unset($article); ?>';
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
    public function tagList($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input(\'param.cid/f\')}';
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "listing.query",
                            cid:    "' . $_tag['cid'] . '",
                            sign:   jQuery.sign({
                                method: "listing.query",
                                cid:    "' . $_tag['cid'] . '"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
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
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input(\'param.cid/f\')';
            $parseStr  = '<?php $list = logic(\'cms/listing\')->query(' . $_tag['cid'] . ');';
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
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method:   "banner.query",
                            slide_id: "' . $_tag['id'] . '",
                            sign:     jQuery.sign({
                                method:   "banner.query",
                                slide_id: "' . $_tag['id'] . '"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $banner = logic(\'cms/banner\')->query(\'' . $_tag['id'] . '\');';
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
                    $.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method:  "ads.query",
                            ads_id:  "' . $_tag['id'] . '",
                            sign:    jQuery.sign({
                                method: "ads.query",
                                ads_id: "' . $_tag['id'] . '"
                                })
                        }
                    }, function(result){
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $ads = logic(\'cms/ads\')->query(\'' . $_tag['id'] . '\');';
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "sidebar.query",
                            cid:    "{:input(\'param.cid/f\', 0)}",
                            sign:   jQuery.sign({
                                method: "sidebar.query",
                                cid:    "{:input(\'param.cid/f\', 0)}"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
                        } else if (result.code !== "SUCCESS") {
                            return false;
                        }
                        if (result.data) {
                            var data = result.data;';
            if (!empty($_tag['ele'])) {
                $parseStr .= 'jQuery("' . $_tag['ele'] . '").html(data.name);';
            }
            $parseStr .= 'for (var key in data.child) {
                                var vo = data.child[key];
                                ' . $_content . '
                            }
                        }
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $menu = logic(\'cms/sidebar\')->query();';
            $parseStr .= 'if (is_null($menu)) {abort(404);}';
            $parseStr .= '$count = count($menu);';
            $parseStr .= 'foreach ($menu as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($menu, $count, $key, $vo); ?>';
        }

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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        $cid = input('param.cid/f', 0);

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "breadcrumb.query",
                            cid:    "{:input(\'param.cid/f\', 0)}",
                            sign:   jQuery.sign({
                                method: "breadcrumb.query",
                                cid:    "{:input(\'param.cid/f\', 0)}"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $bread = logic(\'cms/breadcrumb\')->query();';
            $parseStr .= 'if (is_null($bread)) {abort(404);}';
            $parseStr .= '$count = count($bread);';
            $parseStr .= 'foreach ($bread as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($bread, $count, $key, $vo); ?>';
        }

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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method:  "nav.query",
                            type_id: "' . $_tag['type'] . '",
                            sign:    jQuery.sign({
                                method:  "nav.query",
                                type_id: "' . $_tag['type'] . '"
                                })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404', [], 'html', true) . '");
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $nav = logic(\'cms/nav\')->query(\'' . $_tag['type'] . '\');';
            $parseStr .= 'if (is_null($nav)) {abort(404);}';
            $parseStr .= '$count = count($nav);';
            $parseStr .= 'foreach ($nav as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($nav, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }

    /**
     * 标签解析
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagQuery($_tag, $_content)
    {
        $parseStr  = '<?php $data = Db::query(' . $_tag['sql'] . ')';

        $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';

        return $parseStr;
    }
}
