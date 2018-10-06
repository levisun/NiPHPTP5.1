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
                        animation: true,
                        type: "get",
                        data: {
                            method: "tags.query",
                            token: "' . $this->getToken() . '",
                            sign: "' . $this->sign([
                                'method' => 'tags.query',
                                'token'  => $this->getToken()
                                ]) . '"
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : '{:input(\'param.cid/f\')}';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '{:input(\'param.id/f\')}';

            $time = time();
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "article.query",
                            token: "' . $this->getToken() . '",
                            cid: "' . $_tag['cid'] . '",
                            id: "' . $_tag['id'] . '",
                            sign: "' . $this->sign([
                                'method'    => 'article.query',
                                'token'     => $this->getToken(),
                                'cid'       => $_tag['cid'],
                                'id'        => $_tag['id']
                                ]) . '"
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
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            token: "' . $this->getToken() . '",
                            timestamp: "' . $time . '",
                            cid: "' . $_tag['cid'] . '",
                            id: "' . $_tag['id'] . '",
                            sign: "' . $this->sign([
                                'method'    => 'article.hits',
                                'token'     => $this->getToken(),
                                'timestamp' => $time,
                                'cid'       => $_tag['cid'],
                                'id'        => $_tag['id']
                                ]) . '"
                        }
                    }, function(){
                    });
                });
                </script>';
        } else {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : 'input(\'param.cid/f\')';
            $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : 'input(\'param.id/f\')';

            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/article\')->query(' . $_tag['cid'] . ', ' . $_tag['id'] . ');';
            $parseStr .= 'if($data === false) {abort(404);}';
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $_tag['cid'] = !empty($_tag['cid']) ? (float) $_tag['cid'] : input('param.cid/f');
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "listing.query",
                            token: "' . $this->getToken() . '",
                            cid: "' . $_tag['cid'] . '",
                            sign: "' . $this->sign([
                                'method' => 'listing.query',
                                'token'  => $this->getToken(),
                                'cid'    => $_tag['cid']
                                ]) . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
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
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/listing\')->query(' . $_tag['cid'] . ');';
            $parseStr .= 'if($data === false) {abort(404);}';
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? (float) $_tag['id'] : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "banner.query",
                            token: "' . $this->getToken() . '",
                            slide_id: "' . $_tag['id'] . '",
                            sign: "' . $this->sign([
                                'method'   => 'banner.query',
                                'token'    => $this->getToken(),
                                'slide_id' => $_tag['id']
                                ]) . '"
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
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
                            token: "' . $this->getToken() . '",
                            ads_id: "' . $_tag['id'] . '",
                            sign: "' . $this->sign([
                                'method' => 'ads.query',
                                'token'  => $this->getToken(),
                                'ads_id' => $_tag['id']
                                ]) . '"
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "sidebar.query",
                            token: "' . $this->getToken() . '",
                            cid: "{:input(\'param.cid/f\', 0)}",
                            sign: "' . $this->sign([
                                'method' => 'sidebar.query',
                                'token'  => $this->getToken(),
                                'cid'    => input('param.cid/f', 0)
                                ]) . '"
                        }
                    }, function(result){
                        if (result.code !== "SUCCESS") {
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
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';
        $cid = input('param.cid/f', 0);

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "breadcrumb.query",
                            token: "' . $this->getToken() . '",
                            cid: "{:input(\'param.cid/f\', 0)}",
                            sign: "' . $this->sign([
                                'method' => 'breadcrumb.query',
                                'token'  => $this->getToken(),
                                'cid'    => input('param.cid/f', 0)
                                ]) . '"
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
        $_tag['type']  = !empty($_tag['type']) ? (float) $_tag['type'] : '2';
        $_tag['async'] = !empty($_tag['async']) ? safe_filter($_tag['async']) : 'true';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        animation: true,
                        type: "get",
                        data: {
                            method: "nav.query",
                            token: "' . $this->getToken() . '",
                            type_id: "' . $_tag['type'] . '",
                            sign: "' . $this->sign([
                                'method'  => 'nav.query',
                                'token'   => $this->getToken(),
                                'type_id' => $_tag['type']
                                ]) . '"
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
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/nav\')->query(\'' . $_tag['type'] . '\');';
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
    public function query($_tag, $_content)
    {
        $parseStr  = '<?php $data = Db::query(' . $_tag['sql'] . ')';

        $parseStr .= '<?php } unset($data, $count, $key, $vo); ?>';

        return $parseStr;
    }

    /**
     * 生成Sign签名
     * @access private
     * @param  array   $_params
     * @return string
     */
    private function sign($_params)
    {
        ksort($_params);

        $str = '';
        foreach ($_params as $key => $value) {
            if (!is_array($value) && $key !== 'sign') {
                $str .= $key . '=' . $value . '&';
            }
        }
        $str = md5(trim($str, '&'));

        return $str;
    }

    /**
     * 获得Token
     * @access private
     * @param
     * @return string
     */
    private function getToken()
    {
        return
        model('common/config')
        ->where([
            ['name', '=', 'ajax_token']
        ])
        ->cache('_CMS_TAGLIB_TOKEN')
        ->value('value');
    }
}
