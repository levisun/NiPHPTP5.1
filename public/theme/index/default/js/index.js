$(document).ready(function(){
    $.pjax({
        url: NIPHP.api.url + "/query/cms.html",
        method: "get",
        data: {
            method: "article.news.query",
            // timestamp: $.timestamp(),
            sign: $.sign({
                method: "article.news.query",
                // timestamp: $.timestamp()
            })
        }
    });
});
