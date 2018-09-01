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
        'nav' => [
            'attr'  => 'type,async',
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
                        data: {
                            method: "tags.query"
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
     * 幻灯片
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagBanner($_tag, $_content)
    {
        $_tag['async'] = !empty($_tag['async']) ? trim($_tag['async']) : 'true';
        $_tag['id']  = !empty($_tag['id']) ? intval($_tag['id']) : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        data: {
                            method: "banner.query",
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
            $parseStr .= 'logic(\'cms/banner\')->query(\'' . $_tag['id'] . '\'); ?>';
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
        $_tag['id']  = !empty($_tag['id']) ? intval($_tag['id']) : '0';

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    $.loading({
                        url: request.api.query,
                        animation: true,
                        data: {
                            method: "ads.query",
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
        $cat_id = input('param.cid/f', 0);

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    var data = '
                     . json_encode(logic('cms/sidebar')->query($cat_id), JSON_UNESCAPED_UNICODE) . ';'
                     . $_content . '
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/sidebar\')->query(\'' . $cat_id . '\');';
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
        $cat_id = input('param.cid/f', 0);

        if ($_tag['async'] == 'true') {
            $parseStr = '<script type="text/javascript">
                $(function(){
                    var data = '
                     . json_encode(logic('cms/breadcrumb')->query($cat_id), JSON_UNESCAPED_UNICODE) . ';'
                     . $_content . '
                });
                </script>';
        } else {
            $parseStr  = '<?php $data = ';
            $parseStr .= 'logic(\'cms/breadcrumb\')->query(\'' . $cat_id . '\');';
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
                    var data = '
                     . json_encode(logic('cms/nav')->query($_tag['type']), JSON_UNESCAPED_UNICODE) . ';'
                     . $_content . '
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
