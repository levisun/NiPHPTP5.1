(function(window){
    var NIPHP = {
        VERSION: "1.0.1",
        APPID: "1000001",
        API_TOKEN: Cookies.get("API_TOKEN"),
        SID: Cookies.get("API_SID")
    };

    /**
     * 组装请求参数
     * @param  array _param  jQuery.serializeArray()
     * @return array
     */
    NIPHP.formParam = function (_param) {
        var data = {};

        if (typeof(_param) == "object") {
            for (var index in _param) {
                data[_param[index]['name']] = _param[index]['value'];
            }
        }

        return data;
    }

    /**
     * 交互
     * 请求
     * @param  array    _param
     * @param  function _callback
     * @return void
     */
    NIPHP.query = function (_param, _callback) {
        jQuery.pjax({
            url: request.api.query,
            headers: NIPHP.HEADER,
            type: "get",
            data: _param,
            success: function(result){
                _callback(result);
            }
        });
    }

    /**
     * 交互
     * 操作
     * @param  array    _param
     * @param  function _callback
     * @return void
     */
    NIPHP.handle = function (_param, _callback) {
        jQuery.pjax({
            url: request.api.handle,
            headers: NIPHP.HEADER,
            async: false,
            type: "post",
            data: _param,
            success: function(result){
                _callback(result);
            }
        });
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

    NIPHP.HEADER = {
        "X-Requested-With": "XMLHttpRequest",
        "X-Request-Id": Cookies.get("API_SID"),
        "X-Request-Token": Cookies.get("API_TOKEN"),
        "X-Request-Timestamp": NIPHP.timestamp()
    };

    window.NIPHP = NIPHP;

})(window);
