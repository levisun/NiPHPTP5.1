(function(window){
    var api = {
        query: "//"+window.location.hostname+""
    };
    var NIPHP = {};

    NIPHP.ads = function () {
        jQuery.pjax({
            url: request.api.query,
            type: "get",
            data: {
                method:  "ads.query",
                token:   "{$Think.const.API_TOKEN}",
                ads_id:  "' . $_tag['id'] . '",
                sign:    jQuery.sign({
                    method: "ads.query",
                    token:  "{$Think.const.API_TOKEN}",
                    ads_id: "' . $_tag['id'] . '"
                })
            },
            success: function(result){
                if (result.code !== "SUCCESS") {
                    return false;
                }
                if (result.data) {
                    var data = result.data;
                    for (var key in data) {
                        var vo = data[key];
                        ' . $_content . '
                    }
                }
            }
        });
    }

    window.NIPHP = NIPHP;

})(window);
