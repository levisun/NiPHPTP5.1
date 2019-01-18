(function(window){
    var NIPHP = {
        VERSION: "1.0.1",
        APPID: "1000001",
        API_TOKEN: Cookies.get("API_TOKEN"),
        SID: Cookies.get("API_SID")
    };


    NIPHP.redirect = function (_url) {
        window.location.href = _url;
    }

    /**
     * 组装请求参数
     * @param  array _param  jQuery.serializeArray()
     * @return array
     */
    NIPHP.formParam = function (_param) {
        var data = {
            token: NIPHP.API_TOKEN,
            sid: NIPHP.SID,
            timestamp: NIPHP.timestamp(),
        };

        for (var index in _param) {
            data[_param[index]['name']] = _param[index]['value'];
        }

        return data;
    }

    /**
     * 设置类变量
     * @param  array _param
     * @return void
     */
    NIPHP.set = function (_param) {
        for (var index in _param) {
            NIPHP[index] = _param[index];
        }
    }

    /**
     * 时间戳
     */
    NIPHP.timestamp = function () {
        var timestamp = Date.parse(new Date());
        return timestamp /1000;
    }

    window.NIPHP = NIPHP;

})(window);
