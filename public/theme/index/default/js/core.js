layui.extend({
    np: '{/}' + NIPHP.cdn.js + 'np' // {/}的意思即代表采用自有路径，即不跟随 base 路径
})
layui.use(['jquery', 'np'], function(){
    var jQuery = layui.jquery;
    var np = layui.np;

    // 初始化导航
    np.pjax({
        url: NIPHP.api.url + '/cms.html',
        method: 'get',
        data: {
            method: 'nav.main.query',
            sign: np.sign({
                method: 'nav.main.query'
            })
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

    np.pjax({
        url: NIPHP.api.url + '/cms.html',
        method: 'get',
        data: {
            method: 'nav.sidebar.query',
            cid: NIPHP.param.cid,
            sign: np.sign({
                method: 'nav.sidebar.query'
            })
        }
    });
});
