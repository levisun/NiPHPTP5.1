layui.extend({
    np: '{/}' + NIPHP.cdn.js + 'np' // {/}的意思即代表采用自有路径，即不跟随 base 路径
})
layui.use(['jquery', 'np'], function(){
    var jQuery = layui.jquery;
    var np = layui.np;


    np.pjax({
        url: NIPHP.api.url + '/query/cms.html',
        method: 'get',
        data: {
            method: 'article.catalog.query',
            cid: NIPHP.param.cid,
            sign: np.sign({
                method: 'article.catalog.query'
            })
        },
        success: function(result) {
            if (result.code == 'SUCCESS') {

            }
        }
    });
});
