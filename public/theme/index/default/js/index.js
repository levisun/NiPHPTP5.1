$(document).ready(function(){
    $.pjax({
        url: "//api.tp5.com/query/cms.html",
        method: "get",
        data: {
            method: "article.news.query",
            timestamp: $.timestamp(),
            sign: $.sign({
                method: "article.news.query",
                timestamp: $.timestamp()
            })
        }
    });
});
