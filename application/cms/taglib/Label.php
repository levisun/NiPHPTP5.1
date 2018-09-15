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
        'tags' => [
            'attr'  => '',
            'close' => 1,
            'alias' => 'tag',
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "tags.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/tags\')->query(); ?>';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';
        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input(\'param.cid/f\')}';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '{:input(\'param.id/f\')}';

            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "article.find",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            cid: "' . $_tag['cid'] . '",
                            id: "' . $_tag['id'] . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {

                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                    $.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            timestamp: "{:time()}",
                            cid: "' . $_tag['cid'] . '",
                            id: "' . $_tag['id'] . '"
                        }
                    }, function(){
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input(\'param.cid/f\')';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : 'input(\'param.id/f\')';

            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/article\')->find(' . $_tag['cid'] . ', ' . $_tag['id'] . ');';
            $parseStr .= 'if($data === false) {redirect(url(\'error/page\', [\'code\' => 404], \'html\', true));}';
            $parseStr .= '?>';
            $parseStr .= $_content;
            $parseStr .= '<?php unset($data); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input(\'param.cid/f\')}';
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "article.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            cid: "' . $_tag['cid'] . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            $.redirect("' . url('error/page', ['code' => 404], 'html', true) . '");
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input(\'param.cid/f\')';
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/article\')->query(' . $_tag['cid'] . ');';
            $parseStr .= 'if($data === false) {redirect(url(\'error/page\', [\'code\' => 404], \'html\', true));}';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data[\'list\'] as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "banner.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            slide_id: "' . $_tag['id'] . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/banner\')->query(\'' . $_tag['id'] . '\');';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "ads.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            ads_id: "' . $_tag['id'] . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/ads\')->query(\'' . $_tag['id'] . '\'); ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php unset($data); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "sidebar.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            cid: "{:input(\'param.cid/f\', 0)}"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/sidebar\')->query();';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
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
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';
        $cid = input('param.cid/f', 0);

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "breadcrumb.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            cid: "{:input(\'param.cid/f\', 0)}"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/breadcrumb\')->query();';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
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
        $_tag['type']  = !empty($_tag['type']) ? intval($_tag['type']) : '2';
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "nav.query",
                            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
                            type_id: "' . $_tag['type'] . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
                            return false;
                        }
                        var data = result.data;
                        ' . $_content . '
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/nav\')->query(\'' . $_tag['type'] . '\');';
            $parseStr .= '$count = count($data);';
            $parseStr .= 'foreach ($data as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }
}
