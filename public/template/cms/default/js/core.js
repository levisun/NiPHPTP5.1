layui.config({
    dir: '/static/layui/' //layui.js 所在路径
}).extend({
    niphp: '{/}' + NIPHP.cdn.static + 'layui.niphp', // {/}的意思即代表采用自有路径，即不跟随 base 路径
});
layui.use(['jquery', 'laypage', 'niphp'], function(){
    var jQuery = layui.jquery;
    var np = layui.niphp;

    // 初始化导航
    np.pjax({
        url: NIPHP.api.url + '/cms.do',
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

    if (NIPHP.param.cid) {
        // 侧导航
        np.pjax({
            url: NIPHP.api.url + '/cms.do',
            method: 'get',
            data: {
                method: 'nav.sidebar.query',
                cid: NIPHP.param.cid
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
        np.pjax({
            url: NIPHP.api.url + '/cms.do',
            method: 'get',
            data: {
                method: 'nav.breadcrumb.query',
                cid: NIPHP.param.cid
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







    // np.pjax({
    //     url: NIPHP.api.url + '/upload/cms.do',
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
