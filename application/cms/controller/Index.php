<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index extends Base
{
    /**
     * 首页
     * @access public
     * @param
     * @return mixed
     */
    public function index()
    {
        $res =  date('Y-m-d H:i:s');
        $res .= safe_filter('<div class="smart-widget">
    <div class="smart-widget-header">+
        1

        <span class="smart-widget-option">
            <a href="{:url(\'\', array(\'operate\' => \'added\'))}" class="btn btn-default btn-xs">
                <span class="fa fa-plus"></span>\{:lang(\'button add\')}
            </a>
        </span>
    </div>
    <div class="smart-widget-inner">
        <div class="smart-widget-body">
            <form action="" method="post" class="form-horizontal">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>{:lang(\'id\')}</th>
                            <th>{:lang(\'ads name\')}</th>
                            <th>{:lang(\'size\')}</th>
                            <th>{:lang(\'time\')}</th>
                            <th>{:lang(\'operate\')}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </form>
            <div id="paging">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$.loading({
    url: request.api.query,
    animation: true,
    data: {
        token: "c2630911e31549d4ddb556daba9c20d9c910d396",
        timestamp: "{:time()}",
        method: "content.ads.query"
    }
}, function(result){
    if (result.code !== "SUCCESS") {
        return false;
    }

    var list = "";
    for (var index in result.data.list) {
        list = list + "<tr>";
        list = list + "<td>"+result.data.list[index].id+"</td>";
        list = list + "<td>"+result.data.list[index].name+"</td>";
        list = list + "<td>"+result.data.list[index].width+" X "+result.data.list[index].height+"</td>";
        list = list + "<td>"+result.data.list[index].start_time+" - "+result.data.list[index].end_time+"</td>";

        list = list + "<td>";
        list = list + "<a href=\'"+result.data.list[index].url.editor+"\'><span class=\'fa fa-edit\'></span>{:lang(\'button editor\')}</a>";
        list = list + "<a class=\'np-btn-remove\' data-id=\'"+result.data.list[index].id+"\'><span class=\'fa fa-edit\'></span>{:lang(\'button remove\')}</a>";
        list = list + "</td>";

        list = list + "</tr>";
    }
    $("#dataTable tbody").prepend(list);
    $("#paging").prepend(result.data.page);
    pagingToAjax();
});

$(document).on("click", "#paging a", function(){
    var page = $.urlParam("p", "", $(this).attr("url"));
    $.loading({
        url: request.api.query,
        animation: true,
        data: {
            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
            timestamp: "{:time()}",
            method: "content.ads.query",
            pid: "{$Request.param.pid}",
            p: page
        }
    }, function(result){
        if (result.code !== "SUCCESS") {
            return false;
        }
        $("#dataTable tbody tr").remove();
        $("#paging ul").remove();

        var list = "";
        for (var index in result.data.list) {
            list = list + "<tr>";
            list = list + "<td>"+result.data.list[index].id+"</td>";
            list = list + "<td>"+result.data.list[index].name+"</td>";
            list = list + "<td>"+result.data.list[index].width+" X "+result.data.list[index].height+"</td>";
            list = list + "<td>"+result.data.list[index].start_time+" - "+result.data.list[index].end_time+"</td>";

            list = list + "<td>";
            list = list + "<a href=\'"+result.data.list[index].url.editor+"\'><span class=\'fa fa-edit\'></span>{:lang(\'button editor\')}</a>";
            list = list + "<a class=\'np-btn-remove\' data-id=\'"+result.data.list[index].id+"\'><span class=\'fa fa-edit\'></span>{:lang(\'button remove\')}</a>";
            list = list + "</td>";

            list = list + "</tr>";
        }
        $("#dataTable tbody").prepend(list);
        $("#paging").prepend(result.data.page);
        pagingToAjax();
    });
});

$(document).on("click", ".np-btn-remove", function(){
    var id = $(this).attr("data-id");
    $.loading({
        url: request.api.settle,
        async: false,
        data: {
            token: "c2630911e31549d4ddb556daba9c20d9c910d396",
            timestamp: "{:time()}",
            method: "content.ads.remove",
            id: id
        },
    }, function(result){
        handle(result, false, false);
        if (result.code === "SUCCESS") {
            $.reload();
        }
    });
    return false;
});￥
</script><?> select * form tablename where 1 ￥ limit 100');

        // $res = 'dddd';
        echo $res;die();
        // return $this->fetch('index.html');
    }

    /**
     * 列表页
     * @access public
     * @param
     * @return mixed
     */
    public function entry($operate = '')
    {
        $tpl = $operate ? $operate : '';
        return $this->fetch($operate . '.html');
    }

    /**
     * 频道页
     * @access public
     * @param
     * @return mixed
     */
    public function channel()
    {
        halt('channel');
        return $this->fetch('channel.html');
    }

    /**
     * 反馈
     * @access public
     * @param
     * @return mixed
     */
    public function feedback()
    {
        halt('feedback');
        return $this->fetch('feedback.html');
    }

    /**
     * 留言
     * @access public
     * @param
     * @return mixed
     */
    public function message()
    {
        halt('message');
        return $this->fetch('message.html');
    }

    /**
     * 标签
     * @access public
     * @param
     * @return mixed
     */
    public function tags()
    {
        return $this->fetch('tags.html');
    }

    /**/
    public function go()
    {
        # code...
    }

    /**
     * IP信息
     * @access public
     * @param
     * @return mixed
     */
    public function getipinfo()
    {
        json(logic('common/IpInfo')->getInfo());
    }
}
