layui.config({
    dir: '/static/layui/' //layui.js 所在路径
}).extend({
    np: '{/}' + NIPHP.cdn.js + 'np', // {/}的意思即代表采用自有路径，即不跟随 base 路径
});
layui.use(['jquery', 'laypage', 'np'], function(){
    var jQuery = layui.jquery;
    var np = layui.np;

    // 初始化导航
    np.pjax({
        url: NIPHP.api.url + '/cms.html',
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
        np.pjax({
            url: NIPHP.api.url + '/cms.html',
            method: 'get',
            data: {
                method: 'nav.sidebar.query',
                cid: NIPHP.param.cid
            }
        });

        np.pjax({
            url: NIPHP.api.url + '/cms.html',
            method: 'get',
            data: {
                method: 'article.catalog.query',
                cid: NIPHP.param.cid
            },
            success: function(result) {
                if (result.code == 'SUCCESS') {

                }
            }
        });

        np.pjax({
            url: NIPHP.api.url + '/cms.html',
            method: 'get',
            data: {
                method: 'nav.breadcrumb.query',
                cid: NIPHP.param.cid
            },
            success: function(result) {
                if (result.code == 'SUCCESS') {

                }
            }
        });
    }



    np.pjax({
        url: NIPHP.api.url + '/cms.html',
        method: 'get',
        data: {
            method: 'article.details.query',
            id: 1
        },
        success: function(result) {
            if (result.code == 'SUCCESS') {

            }
        }
    });

    np.pjax({
        url: NIPHP.api.url + '/cms.html',
        method: 'get',
        data: {
            method: 'article.details.hits',
            id: 1
        },
        success: function(result) {
            if (result.code == 'SUCCESS') {

            }
        }
    });

    // np.pjax({
    //     url: NIPHP.api.url + '/upload/cms.html',
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
