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
     * 弹出层
     */
    jQuery.uiPopup = function (_params, _status) {
        _status = _status ? _status : "init";

        if (_status === "init") {
            var style = _params.style ? "nipui-popup-" + _params.style : "nipui-popup-popup";

            var html = "<div class='nipui-popup "+style+"'><div class='nipui-popup-mask'></div><div class='nipui-popup-container'><div class='nipui-pull-right nipui-close' style='margin: 10px' bindtap=''></div>";
            if (_params.title) {
                html += "<div class='nipui-popup-header'>"+_params.title+"</div>";
            }
            if (_params.content) {
                html += "<div class='nipui-popup-content'>"+_params.content+"</div>";
            }
            if (_params.footer) {
                html += "<div class='nipui-popup-footer'>"+_params.footer+"</div>";
            }
            html += "</div></div>";
            html += "<script type='text/javascript'>$('.nipui-close').click(function(){$.uiPopup({}, 'hide');});$('.nipui-popup-mask').click(function(){$.uiPopup({}, 'hide');});$('"+_params.element+"').click(function(){$.uiPopup({}, 'show');});</script>";
            jQuery("body").append(html);
        } else if (_status === "show") {
            jQuery(".nipui-popup").addClass("nipui-popup-show");
        } else if (_status === "hide") {
            jQuery(".nipui-popup").removeClass("nipui-popup-show");
        }
    }

    /**
     * 加载弹框提示
     */
    jQuery.uiLoadpopup = function (_tips, _timeout, _element) {
        _tips = _tips !== "" ? _tips : "加载中...";
        _timeout = _timeout ? _timeout : 15;
        _element = _element ? _element : "body";

        if (_tips === false) {
            jQuery("div.nipui-loadpopup").remove();
            jQuery("body").removeAttr("style");
        } else {
            var html = "";
            jQuery("body").css({"height": "100%", "overflow": "hidden"});
            html  = "<div class='nipui-loadpopup'>";
            html += "<div class='nipui-loadpopup-mask'></div>";
            html += "<div class='nipui-loadpopup-tips'>";
            html += "<div class='nipui-loadpopup-loading'></div>"+_tips;
            html += "</div>";
            html += "</div>";
            jQuery(_element).append(html);

            setTimeout(function(){
                jQuery.uiLoadpopup(false);
            }, _timeout * 1000);
        }
    }

    /**
     * 轻加载提示
     */
    jQuery.uiLoadmore = function (_tips, _element) {
        _tips = _tips !== "" ? _tips : "加载中...";
        _element = _element ? _element : "body";

        if (_tips === false) {
            jQuery("div.nipui-loadmore").remove();
        } else {
            var html = "";
            if (_tips === "nodata") {
                html  = "<div class='nipui-loadmore nipui-loadmore-nodata'>";
                html += "<div class='nipui-loadmore-tips'>我可是有底线的^_^</div>";
                html += "</div>";
            } else {
                html  = "<div class='nipui-loadmore'>";
                html += "<div class='nipui-loading'></div>";
                html += "<div class='nipui-loadmore-tips'>"+_tips+"</div>";
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

        var html = "<div class='nipui-toast-mask'></div><div class='nipui-toast-tips'>"+_tips+"</div>";
        $("body").append(html);

        setTimeout(function(){
            $("div.nipui-toast-mask").remove();
            $("div.nipui-toast-tips").remove();
        }, _time * 1000);
    }

    /**
     * 签名
     */
    jQuery.sign = function (_params) {
        // 先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
        var newkey = Object.keys(_params).sort();

        // 创建一个新的对象，用于存放排好序的键值对
        var newObj = {};
        for(var i = 0; i < newkey.length; i++) {
            // 遍历newkey数组
            newObj[newkey[i]] = _params[newkey[i]];
            // 向新创建的对象中按照排好的顺序依次增加键值对
        }

        var sign = "";
        for (var index in newObj) {
            sign += index + "=" + newObj[index] + "&";
        }
        sign = sign.substr(0, sign.length - 1);
        sign = md5(sign);

        return sign;
    }

    /**
     * HTML转义
     */
    jQuery.htmlDecode = function (_string) {
        _string = _string.toString();
        _string = _string.replace(/&amp;/g, '&');
        _string = _string.replace(/&lt;/g, '<');
        _string = _string.replace(/&gt;/g, '>');
        _string = _string.replace(/&quot;/g, '"');
        _string = _string.replace(/&#039;/g, '\'');

        return _string;
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
     * 安卓端访问
     */
    jQuery.isAndroid = function () {
        var reg = /(android)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result !== null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 苹果端访问
     */
    jQuery.isIos = function () {
        var reg = /(iphone|ipad|ipod|ios)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result !== null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是否微信端访问
     */
    jQuery.isWechat = function () {
        var reg = /(micromessenger)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result !== null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是否移动端访问
     */
    jQuery.isMobile = function () {
        var reg = /(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |googlebot-mobile|yahooseeker\/m1a1-r2d2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result !== null) {
            return true;
        } else {
            return false;
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
     * 数据安全过滤
     */
    jQuery.safe_filter = function (_string) {
        var pattern = new RegExp("[`~!@#$%^&*()_+-={}|\\[\\]\\:\";'<>?,./”“；：。，、？——【】｛｝（）·￥……]");
        var specialStr = "";
        for (var i = 0; i < _string.length; i++) {
            specialStr += _string.substr(i, 1).replace(pattern, '');
        }
        return specialStr;
    }

    /**
     * URL get参数
     */
    jQuery.url = function (_key, _default, _url) {
        _key = _key ? _key : false;
        _default = _default ? _default : "";
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
     * 时间戳
     */
    jQuery.timestamp = function () {
        var timestamp = Date.parse(new Date());
        return timestamp /1000;
    }

    /**
     * 域名
     */
    jQuery.domain = function (_url) {
        _url = _url ? _url : false;
        var domain  = window.location.protocol + "//";
            domain += window.location.hostname;

        if (_url) {
            domain += window.location.pathname;
        }
        return domain;
    }

    /**
     * 上传
     */
    jQuery.upload = function (_params) {
        var defaults = {
            async: false,
            cache: false,
            processData: false,
            contentType: false
        };
        _params = jQuery.extend(true, defaults, _params);

        var xhr = jQuery.ajax(_params);
        if (xhr.readyState > 0) {

        }
        return xhr;
    }

    /**
     * 点击加载更多
     */
    jQuery.click_more = function (_params) {
        var page = "click_more-"+_params.flag+"-page";
        var bool = "click_more-"+_params.flag+"-bool";
        jQuery("body").attr(page, 1);
        jQuery("body").attr(bool, "true");

        jQuery(document).on("click", _params.element, function(){
            var is = jQuery("body").attr(bool);
            if (is == "true" ) {
                var num = jQuery("body").attr(page);
                    num++;

                _params.data.page = num;
                jQuery("body").attr(page, num);
                jQuery("body").attr(bool, "false");

                var xhr = jQuery.pjax(_params);
                if (xhr.readyState > 0) {
                    setTimeout(function(){
                        jQuery("body").attr(bool, "true");
                    }, 1500);
                }
                return xhr;
            }
        });
    }

    /**
     * 滚动加载更多
     */
    jQuery.scroll_more = function (_params) {
        var page = "more-"+_params.flag+"-page";
        var bool = "more-"+_params.flag+"-bool";
        jQuery("body").attr(page, 1);
        jQuery("body").attr(bool, "true");

        jQuery(window).scroll(function(){
            var is = jQuery("body").attr(bool);
            if (is == "true" && jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) - 200) {
                var num = jQuery("body").attr(page);
                    num++;

                _params.data.page = num;
                jQuery("body").attr(page, num);
                jQuery("body").attr(bool, "false");

                var xhr = jQuery.pjax(_params);
                if (xhr.readyState > 0) {
                    setTimeout(function(){
                        jQuery("body").attr(bool, "true");
                    }, 1500);
                }
                return xhr;
            }
        });
    }

    /**
     * pjax
     */
    jQuery.pjax = function (_params) {
        var defaults = {
            push: false,                        // 添加历史记录
            replace: false,                     // 替换历史记录
            scrollTo: false,                    // 是否回到顶部 可定义顶部像素
            requestUrl: window.location.href,   // 重写地址
            type: "GET",
            contentType: "application/x-www-form-urlencoded"
        };
        _params = jQuery.extend(true, defaults, _params);

        // 回到顶部
        if (_params.scrollTo !== false) {
            jQuery("html,body").animate({
                scrollTop: _params.scrollTo
            }, 300);
        }

        // 设置头部
        _params.beforeSend = function (xhr) {
            xhr.setRequestHeader("HTTP_X_PJAX", true);
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

    jQuery.popstateEvent = function (_params) {
        $(window).unbind("popstate");

        // 函数方法
        if (typeof(_params) === "function") {
            $(window).bind("popstate", function(result) {
                _params();
            });
        }

        // 参数
        else if (typeof(_params) === "object") {
            $(window).bind("popstate", _params, function(result) {
                jQuery.pjax(_params);
            });
        }

        else {
            // alert(typeof(_params));
        }


        // $(window).bind("popstate", _params, function(result) {
        //     jQuery.pjax(_params);
        // });
    }
}));
