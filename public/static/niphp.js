(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function (jQuery) {

    /**
     * 异步请求
     */
    jQuery.pjax = function (_params) {
        var defaults = {
            push: false,                        // 添加历史记录
            replace: false,                     // 替换历史记录
            scrollTo: false,                    // 是否回到顶部 可定义顶部像素
            requestUrl: window.location.href,   // 重写地址
            type: 'GET',
            contentType: 'application/x-www-form-urlencoded'
        };
        _params = jQuery.extend(true, defaults, _params);

        _params.data.sign = jQuery.sign({
            method: _params.data.method
        });

        // 设置头部
        _params.beforeSend = function (xhr) {
            xhr.setRequestHeader('Accept', 'application/vnd.' + NIPHP.api.root + '.v' + NIPHP.api.version + '+json');
            xhr.setRequestHeader('Authorization', NIPHP.api.authorization);
        }

        var xhr = jQuery.ajax(_params);

        if (xhr.readyState > 0) {
            // 添加历史记录
            if (_params.push === true) {
                window.history.pushState(null, document.title, _params.requestUrl);
            }

            // 替换历史记录
            else if (_params.replace === true) {
                window.history.replaceState(null, document.title, _params.requestUrl);
            }
        }

        return xhr;
    }

    /**
     * 时间戳
     */
    jQuery.timestamp = function(){
        var timestamp = Date.parse(new Date());
        return timestamp / 1000;
    }

    /**
     * 签名
     */
    jQuery.sign = function(_params){
        // 先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
        var newkey = Object.keys(_params).sort();

        // 创建一个新的对象，用于存放排好序的键值对
        var newObj = {};
        for(var i = 0; i < newkey.length; i++) {
            // 遍历newkey数组
            newObj[newkey[i]] = _params[newkey[i]];
            // 向新创建的对象中按照排好的顺序依次增加键值对
        }

        var sign = '';
        for (var index in newObj) {
            sign += index + '=' + newObj[index] + '&';
        }
        sign = sign.substr(0, sign.length - 1);
        sign = md5(sign);

        return sign;
    }
}));
