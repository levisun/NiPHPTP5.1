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
     * 轻加载提示
     */
    jQuery.uiLoadmore = function (_tips, _element) {
        _tips = _tips !== "" ? _tips : "加载中...";
        _element = _element ? _element : "body";

        if (_tips === false) {
            jQuery("div.layoutUi-loadmore").remove();
        } else {
            var html = "";
            if (_tips === "nodata") {
                html  = "<div class='layoutUi-loadmore layoutUi-loadmore-nodata'>";
                html += "<div class='layoutUi-loadmore-tips'>我可是有底线的^_^</div>";
                html += "</div>";
            } else {
                html  = "<div class='layoutUi-loadmore'>";
                html += "<div class='layoutUi-loading'></div>";
                html += "<div class='layoutUi-loadmore-tips'>"+_tips+"</div>";
                html += "</div>";
            }
            jQuery(_element).append(html);
        }
    }

    /**
     * 轻提示
     */
    jQuery.uiToast = function (_tips, _time) {
        _tips = _tips ? _tips : "";
        _time = _time ? _time : 1.5;

        var html = "<div class='layoutUi-toast-mask'></div><div class='layoutUi-toast-tips'>"+_tips+"</div>";
        $("body").append(html);

        setTimeout(function(){
            $("div.layoutUi-toast-mask").remove();
            $("div.layoutUi-toast-tips").remove();
        }, _time * 1000);
    }

    /**
     * 滚动到指定位置
     */
    jQuery.scrollElement = function (_element, _time) {
        _time = _time ? _time : 0.5;

        jQuery("html,body").animate({
            scrollTop: jQuery(_element).offset().top
        }, _time * 1000);
        return false;
    }

    /**
     * 回到顶部
     */
    jQuery.scrollTop = function (_element) {
        // 监听滚动
        jQuery(window).scroll(function () {
            if (jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) / 2) {
                jQuery(_element).fadeIn(1000);
            } else {
                jQuery(_element).fadeOut(500);
            }
        });

        // 点击回到顶部
        jQuery(_element).click(function(){
            jQuery("html,body").animate({
                scrollTop: 0
            }, 300);
        });
    }

    /**
     * 判断是否微信端访问
     */
    jQuery.isWechat = function () {
        var reg = /(MicroMessenger)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 判断是否移动端访问
     */
    jQuery.isMobile = function () {
        var reg = /(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 是否选中
     */
    jQuery.isChecked = function(_element) {
        return jQuery(_element).is(":checked");
    };

    /**
     * 重新加载页面
     */
    jQuery.reload = function() {
        window.location.reload();
    }

    /**
     * 重定向
     */
    jQuery.redirect = function(_url) {
        window.location.href = _url;
    }

    /**
     * 重写URL地址[不刷新]
     */
    jQuery.rewriteUrl = function(_url) {
        history.replaceState(null, null, _url);
    }

    /**
     * 分割字符串为数组
     */
    jQuery.explode = function (_delimiter, _string) {
        string = _string.toString();
        var array = new Array();
        array = string.split(_delimiter);
        return array;
    }

    /**
     * 数组转字符串
     */
    jQuery.implode = function (_glue, _pieces) {
        var string = _pieces.join(_glue);
        return string;
    }

    /**
     * 搜索数组中是否存在指定的值
     */
    jQuery.in_array = function (_search, _array){
        for(var index in _array){
            if(_array[index] == _search){
                return true;
            }
        }
        return false;
    }

    /**
     * URL get参数
     */
    jQuery.url = function (_key, _default, _url) {
        _key = _key ? _key : false;
        _default = _default ? _default : false;
        _url = _url ? _url : false;

        if (_url) {
            var result = this.explode("?", _url);
            var params = result[1] ? $result : "";
        } else {
            var params = window.location.search.substr(1);
        }

        if (params) {
            var reg = new RegExp("(^|&)" + _key + "=([^&]*)(&|$)");
            var result = params.match(reg);
            var value = decodeURIComponent(result[2]);
        } else {
            var reg = new RegExp("(/" + _key + ")[\-|/]([0-9a-zA-Z_%]*)(/|.)");
            var params = _url ? _url : window.location.href;
            params = params.toString();

            var result = params.match(reg);
            if (result) {
                var value = decodeURIComponent(result[2]);
            }
        }

        return value ? value : _default;
    }

    /**
     * 上传
     */
    jQuery.upload = function (_options) {
        _options = jQuery.extend(true, jQuery.ajaxSettings, _options);
        _options.async       = false;
        _options.cache       = false;
        _options.processData = false;
        _options.contentType = false;

        var xhr = jQuery.ajax(_options);
        if (xhr.readyState > 0) {

        }
        return xhr;
    }

    /**
     * 加载更多
     */
    jQuery.more = function (_options) {
        var defaults = {
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded"
        };

        _options = jQuery.extend(true, jQuery.ajaxSettings, defaults, _options);

        // 设置头部
        _options.beforeSend = function (xhr) {
            xhr.setRequestHeader("HTTP_X_PJAX", true);
        }

        var page = "loading-"+_options.scrollMore+"-page";
        var bool = "loading-"+_options.scrollMore+"-bool";
        jQuery("body").attr(page, 1);
        jQuery("body").attr(bool, "true");

        jQuery(window).scroll(function(){
            var is = jQuery("body").attr(bool);
            if (is == "true" && jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) - 10) {

                var num = jQuery("body").attr(page);
                    num++;

                jQuery("body").attr(page, num);
                jQuery("body").attr(bool, "false");

                var xhr = jQuery.ajax(_options);
                if (xhr.readyState > 0) {
                    jQuery("body").attr(bool, "true");

                    // 添加历史记录
                    if (_options.push === true) {
                        window.history.pushState(null, "", window.location.href);
                    }

                    // 替换历史记录
                    else if (_options.replace === true) {
                        window.history.replaceState(null, "", window.location);
                    }
                }
                return xhr;
            }
        });
    }

    /**
     * pjax
     */
    jQuery.pjax = function (_options) {
        var defaults = {
            push: false,        // 添加历史记录
            replace: false,     // 替换历史记录
            scrollTo: false,    // 是否回到顶部 可定义顶部像素
            scrollMore: false,  // 加载更多

            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded"
        };
        _options = jQuery.extend(true, jQuery.ajaxSettings, defaults, _options);

        // 回到顶部
        if (_options.scrollTo !== false) {
            jQuery("html,body").animate({
                scrollTop: _options.scrollTo
            }, 300);
        }

        // 设置头部
        _options.beforeSend = function (xhr) {
            xhr.setRequestHeader("HTTP_X_PJAX", true);
        }

        // 加载更多
        if (_options.scrollMore !== false) {
            jQuery.loadMore(_options);
        }

        var xhr = jQuery.ajax(_options);
        if (xhr.readyState > 0) {
            // 添加历史记录
            if (_options.push === true) {
                window.history.pushState(null, "", window.location.href);
            }

            // 替换历史记录
            else if (_options.replace === true) {
                window.history.replaceState(null, "", window.location);
            }
        }

        return xhr;
    }

}));
