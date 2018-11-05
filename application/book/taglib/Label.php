<?php
/**
 *
 * 标签
 *
 * @package   NiPHPCMS
 * @category  application\book\taglib
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\book\taglib;

use think\template\TagLib;

class Label extends TagLib
{
    // 标签定义
    // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    protected $tags = [
        'article' => ['close' => 1, 'attr' => '', 'alias' => 'page'],
        'list'    => ['close' => 1, 'attr' => '', 'alias' => 'list'],
        'more'    => ['close' => 1, 'attr' => '', 'alias' => 'more'],
    ];

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
            $_tag['bid'] = !empty($_tag['bid']) ? (float) $_tag['bid'] : '{:input("param.bid/f")}';
            $_tag['id']  = !empty($_tag['id'])  ? (float) $_tag['id'] : '{:input("param.id/f")}';

            $time = time();
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            timestamp: "' . $time . '",
                            bid:       "' . $_tag['bid'] . '",
                            id:        "' . $_tag['id'] . '",
                            sign:      jQuery.sign({
                                method:    "article.hits",
                                timestamp: "' . $time . '",
                                bid:       "' . $_tag['bid'] . '",
                                id:        "' . $_tag['id'] . '"
                            })
                        }
                    }, function(){
                    });
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.query",
                            bid:   "' . $_tag['bid'] . '",
                            id:    "' . $_tag['id'] . '",
                            sign:  jQuery.sign({
                                method: "article.query",
                                bid:    "' . $_tag['bid'] . '",
                                id:     "' . $_tag['id'] . '"
                            })
                        }
                    }, function(result){
                        if (result.code === "404") {
                            jQuery.redirect("' . url('error/404') . '");
                        } else if (result.code !== "SUCCESS") {
                            return false;
                        }
                        if (result.data) {
                            var data = result.data;
                            jQuery("title").text(data.title+" - "+jQuery("title").text());
                            if (data.keywords) {
                                jQuery("meta[name=\'keywords\']").attr("content", data.title);
                            }
                            if (data.description) {
                                jQuery("meta[name=\'description\']").attr("content", data.title);
                            }
                            ' . $_content . '
                        }
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $article = logic("cms/article")->query();';
            $parseStr .= 'if (is_null($article)) {abort(404);} ?>';
            $parseStr .= $_content;
            $time = time();
            $parseStr .= '<script type="text/javascript">
                jQuery(function(){
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "article.hits",
                            timestamp: "' . $time . '",
                            bid:       "' . $_tag['bid'] . '",
                            id:        "' . $_tag['id'] . '",
                            sign:      jQuery.sign({
                                method:    "article.hits",
                                timestamp: "' . $time . '",
                                bid:       "' . $_tag['bid'] . '",
                                id:        "' . $_tag['id'] . '"
                            })
                        }
                    }, function(){
                    });
                });
                </script>';
        }

        return $parseStr;
    }

    /**
     * AJAX加载更多
     * @access public
     * @param  array  $_tag     标签属性
     * @param  string $_content 标签内容
     * @return string|void
     */
    public function tagMore($_tag, $_content)
    {
        $_tag['bid'] = !empty($_tag['bid']) ? (float) $_tag['bid'] : '{:input("param.bid/f")}';
        $cont = $_content;
        $parseStr = '<script type="text/javascript">
            jQuery(function(){
                jQuery.loadMore({
                    url: request.api.query,
                    type: "get",
                    data: {
                        method: "listing.query",
                        bid:    "' . $_tag['bid'] . '",
                        sign:   jQuery.sign({
                            method: "listing.query",
                            bid:    "' . $_tag['bid'] . '",
                        })
                    }
                }, function(result){
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
                });
            });
            </script>';
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
            $_tag['bid'] = !empty($_tag['bid']) ? (float) $_tag['bid'] : '{:input("param.bid/f")}';
            $cont = $_content;
            $parseStr = '<script type="text/javascript">
                jQuery(function(){
                    var page = window.location.hash;
                    page = page.substr(1, page.length);
                    jQuery.loading({
                        url: request.api.query,
                        type: "get",
                        data: {
                            method: "listing.query",
                            bid:    "' . $_tag['bid'] . '",
                            sign:   jQuery.sign({
                                method: "listing.query",
                                bid:    "' . $_tag['bid'] . '",
                            })
                        }
                    }, function(result){
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
                    });
                });
                </script>';
        } else {
            $parseStr  = '<?php $list = logic("book/listing")->query();';
            $parseStr .= 'if (is_null($list)) {abort(404);}';
            $parseStr .= '$count = count($list["list"]);';
            $parseStr .= 'foreach ($list["list"] as $key => $vo) { ?>';
            $parseStr .= $_content;
            $parseStr .= '<?php } unset($list, $count, $key, $vo); ?>';
        }

        return $parseStr;
    }
}
