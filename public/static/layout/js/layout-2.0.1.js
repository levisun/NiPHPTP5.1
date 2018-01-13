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
}(function ($) {

    /**
     * 弹出层
     */
    $.uiPopup = function (_status = "init", _params) {
        if (_status === "init") {
            var html = "<div class='layoutUi-popup'><div class='layoutUi-popup-mask'></div><div class='layoutUi-popup-container'><div class='layoutUi-pull-right layoutUi-close layoutUiclearDialog' style='margin: 10px' bindtap=''></div>";
            if (this.isset(_params.title)) {
                html += "<div class='layoutUi-popup-header'>"+_params.title+"</div>";
            }
            if (this.isset(_params.content)) {
                html += "<div class='layoutUi-popup-content'>"+_params.content+"</div>";
            }
            if (this.isset(_params.footer)) {
                html += "<div class='layoutUi-popup-footer'>"+_params.footer+"</div>";
            }
            html += "</div></div>";
            $("body").append(html);
        } else if (_status === "show") {
            $(".layoutUi-popup").addClass("layoutUi-popup-show");
        } else if (_status === "hide") {
            $(".layoutUi-popup").removeClass("layoutUi-popup-show");
        }
    }

    /**
     * 轻加载提示
     */
    $.uiLoad = function (_tips, _element = "body") {
        if (_tips === false) {
            $("div.layoutUi-loadmore").remove();
        } else {
            var html = "";
            if (_tips === "nodata") {
                html  = "<div class='layoutUi-loadmore layoutUi-loadmore-nodata'>";
                html += "<div class='layoutUi-loadmore-tips'>我是有底线的！</div>";
                html += "</div>";
            } else {
                html  = "<div class='layoutUi-loadmore'>";
                html += "<div class='layoutUi-loading'></div>";
                html += "<div class='layoutUi-loadmore-tips'>"+_tips+"</div>";
                html += "</div>";
            }
            $(_element).append(html);
        }
    }

    /**
     * 轻提示
     */
    $.uiToast = function (_tips, _time = 1) {
        var html = "<div class='layoutUi-toast-mask'></div><div class='layoutUi-toast-tips'>"+_tips+"</div>";
        $("body").append(html);

        setTimeout(function(){
            $("div.layoutUi-toast-mask").remove();
            $("div.layoutUi-toast").remove();
        }, _time * 1000);
    }


    /**
     * 加载更多
     */
    $.more = function (_params, _callback) {
        $("body").attr("Layout-loading-page", 1);
        $("body").attr("Layout-loading-bool", "true");
        $(window).scroll(function () {
            var is = $("body").attr("Layout-loading-bool");
            if (is == "true" && $(window).scrollTop() >= ($(document).height() - $(window).height()) / 1.05) {
                var page_num = $("body").attr("Layout-loading-page");
                    page_num++;
                $("body").attr("Layout-loading-page", page_num);
                $("body").attr("Layout-loading-bool", "false");

                _params["data"]["p"] = page_num;
                this.loading(_params, function(result){
                    _callback(result);

                    setTimeout(function () {
                        $("body").attr("Layout-loading-bool", "true");
                    }, 1500);
                });
            }
        });
    }

    /**
     * 加载
     */
    $.loading = function (_params, _callback) {
        var ajax_type      = this.isset(_params.type, "post"),
            ajax_url       = this.isset(_params.url, "?ajax_url=undefined"),
            ajax_data      = this.isset(_params.data, ""),
            ajax_async     = this.isset(_params.async, true),
            ajax_cache     = this.isset(_params.cache, false),
            ajax_data_type = this.isset(_params.data_type, "");
        $.ajax({
            type: ajax_type,
            async: ajax_async,
            cache: ajax_cache,
            dataType: ajax_data_type,
            url: ajax_url,
            data: ajax_data,
            success: function (result) {
                _callback(result);
            },
            error: function (result) {
                _callback(result);
            }
        });
    }

    /**
     * 回到顶部
     */
    $.scrollTop = function (_element) {
        // 监听滚动
        $(window).scroll(function () {
            if ($(window).scrollTop() >= ($(document).height() - $(window).height()) / 2) {
                $(_element).fadeIn(1000);
            } else {
                $(_element).fadeOut(500);
            }
        });

        // 点击回到顶部
        $(_element).click(function(){
            $("body,html").animate({scrollTop: 0}, 300);
        });
    }

    /**
     * 判断是否微信端访问
     */
    $.isWechat = function () {
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
    $.isMobile = function () {
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
     * 变量是否存在
     */
    $.isset = function (_name, _default) {
        if (typeof(_name) == "undefined") {
            if (typeof(_default) == "undefined") {
                return false;
            } else {
                return _default;
            }
        } else {
            return _name;
        }
    }

    /**
     * 重新加载页面
     */
    $.reload = function() {
        window.location.reload();
    }

    /**
     * 重定向
     */
    $.redirect = function(_url) {
        window.location.href = _url;
    }

    /**
     * 是否选中
     */
    $.isChecked = function(_element) {
        return jQuery(_element).is(":checked");
    };

    /**
     * 分割字符串为数组
     */
    $.explode = function (_delimiter, _string) {
        string = _string.toString();
        var array = new Array();
        array = string.split(_delimiter);
        return array;
    }

    /**
     * 数组转字符串
     */
    $.implode = function (_glue, _pieces) {
        var string = _pieces.join(_glue);
        return string;
    }

    /**
     * URL get参数
     */
    $.getParam = function (_key) {
        var reg = new RegExp("(^|&)" + _key + "=([^&]*)(&|$)");
        var result = window.location.search.substr(1).match(reg);
        return result ? decodeURIComponent(result[2]) : null;
    }

    /**
     * 转换JSON数据
     */
    $.parseJSON = function (_result) {
        return eval("(" + _result + ")");
    }

}));