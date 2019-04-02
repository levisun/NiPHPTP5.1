layui.config({
    dir: '/static/layui/' //layui.js 所在路径
}).extend({
    nicms: '{/}' + NICMS.cdn.static + 'layui.nicms', // {/}的意思即代表采用自有路径，即不跟随 base 路径
});
layui.use(['jquery', 'laypage', 'nicms'], function(){
    var jQuery = layui.jquery;
    var nc = layui.nicms;

    // 初始化导航
    nc.pjax({
        url: NICMS.api.url + '/cms.do',
        method: 'get',
        data: {
            method: 'nav.main.query'
        },
        success: function(result) {
            if (result.code == 'SUCCESS') {
                new Vue({
                    el: '#header-nav',
                    data: {
                        main_nav: result.data
                    }
                });
                layui.use('element', function(){
                    layui.element.render('nav');
                });
            }
        }
    });

    if (NICMS.param.cid) {
        // 侧导航
        nc.pjax({
            url: NICMS.api.url + '/cms.do',
            method: 'get',
            data: {
                method: 'nav.sidebar.query',
                cid: NICMS.param.cid
            },
            success: function(result) {
                if (result.code == 'SUCCESS') {
                    new Vue({
                        el: '#sidebar',
                        data: {
                            sidebar: result.data
                        }
                    });
                }
            }
        });

        // 面包屑
        nc.pjax({
            url: NICMS.api.url + '/cms.do',
            method: 'get',
            data: {
                method: 'nav.breadcrumb.query',
                cid: NICMS.param.cid
            },
            success: function(result) {
                if (result.code == 'SUCCESS') {
                    new Vue({
                        el: '#breadcrumb',
                        data: {
                            breadcrumb: result.data
                        }
                    });
                }
            }
        });
    }







    // nicms.pjax({
    //     url: NICMS.api.url + '/upload/cms.do',
    //     method: 'post',
    //     data: {
    //         method: 'upload.file.save'
    //     },
    //     success: function(result) {
    //         if (result.code == 'SUCCESS') {

    //         }
    //     }
    // });
});
