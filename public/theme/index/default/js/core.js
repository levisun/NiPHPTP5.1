$(document).ready(function(){
    $.pjax({
        url: NIPHP.api.url + "/query/cms.html",
        method: "get",
        data: {
            method: "site.nav.main",
            // timestamp: $.timestamp(),
            sign: $.sign({
                method: "site.nav.main",
                // timestamp: $.timestamp()
            })
        }
    });
});
