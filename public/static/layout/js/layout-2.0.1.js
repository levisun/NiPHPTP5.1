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

    jQuery.fn.extend({
        /**
         * 轻客户端验证器
         */
        validate: function () {
            var ruleType = {
                "match": /^(.+?)(\d+)-(\d+)$/,
                "*":     /[\w\W]+/,
                "*6-16": /^[\w\W]{6,16}$/,
                "n":     /^\d+$/,
                "n6-16": /^\d{6,16}$/,
                "s":     /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]+$/,
                "s6-18": /^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{6,18}$/,
                "p":     /^[0-9]{6}$/,
                "m":     /^1[0-9]{10}$/,
                "e":     /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
                "url":   /^(\w+:\/\/)?\w+(\.\w+)+.*$/
            };

            var result = {
                result_code:   "SUCCESS",
                result_status: true,
                result_msg:    "SUCCESS"
            };

            this.each(function(){
                var each = {
                    type:       jQuery(this).attr("validate-type"),
                    msg:        jQuery(this).attr("validate-msg"),
                    ":checked": jQuery(this).is(":checked"),
                    name:       jQuery(this).attr("name"),
                    value:      jQuery(this).val()
                };

                // 判断type是否存在
                if (each.type) {
                    // 查找对应正则，没有为自定义正则
                    var rule;
                    for (var index in ruleType) {
                        if (index === each.type) {
                            rule = ruleType[each.type];
                            break;
                        } else {
                            rule = each.type;
                        }
                    }

                    // 验证数据合法
                    if (!each.value.match(rule)) {
                        result = {
                            result_code:   "FAIL",
                            result_status: false,
                            result_msg:    each.msg,
                            result_name:   each.name,
                            result_data:   each.value
                        };
                        return false;
                    }
                }
            });

            return result;
        },

        /**
         * 循环获取元素对应值与内容
         */
        eleEach: function () {
            var each = new Array;
            this.each(function(){
                var arr = {
                    name:       jQuery(this).attr("name"),
                    id:         jQuery(this).attr("id"),
                    class:      jQuery(this).attr("class"),
                    src:        jQuery(this).attr("src"),
                    title:      jQuery(this).attr("title"),

                    value:      jQuery(this).val(),
                    text:       jQuery(this).text(),
                    html:       jQuery(this).html(),
                    width:      jQuery(this).width(),
                    height:     jQuery(this).height(),
                    ":checked": jQuery(this).is(":checked")
                };

                if (arr.src) {
                    // 图片实际宽高
                    var img = new Image();
                    img.src = arr.src;
                    arr["img_width"]  = img.width;
                    arr["img_height"] = img.height;
                }

                each.push(arr);
            });

            return each;
        }
    });

    /**
     * 弹出层
     */
    jQuery.uiPopup = function (_status = "init", _params) {
        if (_status === "init") {
            var style = this.isset(_params.style, "");
            if (style) {
                style = "layoutUi-popup-"+style;
            }

            var html = "<div class='layoutUi-popup "+style+"'><div class='layoutUi-popup-mask'></div><div class='layoutUi-popup-container'><div class='layoutUi-pull-right layoutUi-close' style='margin: 10px' bindtap=''></div>";
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
    jQuery.uiLoad = function (_tips, _element = "body") {
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
    jQuery.uiToast = function (_tips, _time = 1.5) {
        var html = "<div class='layoutUi-toast-mask'></div><div class='layoutUi-toast-tips'>"+_tips+"</div>";
        $("body").append(html);

        setTimeout(function(){
            $("div.layoutUi-toast-mask").remove();
            $("div.layoutUi-toast-tips").remove();
        }, _time * 1000);
    }

    jQuery.domain = function () {
        var path_name = location.pathname;
        var project_name = path_name.substring(0, path_name.substr(1).indexOf("/") + 1);
        var php_self = path_name.substring(
            path_name.substr(1).indexOf("/") + 2,
            path_name.substr(1).indexOf(".php") + 5
            ) + "/";

        if (window.location.host == "localhost") {
            var domain = location.protocol + "//" + window.location.host + project_name + "/";
        } else {
            var domain = location.protocol + "//" + window.location.host + "/";
        }

        return domain;
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
     * 加载更多
     */
    jQuery.more = function (_params, _callback) {
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
                this.loading(_params, function (result) {
                    _callback(result);

                    setTimeout(function () {
                        $("body").attr("Layout-loading-bool", "true");
                    }, 1500);
                });
            }
        });
    }

    /**
     * 上传
     */
    jQuery.upload = function (_params, _callback) {
        _params.type = this.isset(_params.type, "post");

        var form_data = new FormData(_params.file);

        jQuery.ajax({
            url: _params.url,
            type: _params.type,
            cache: false,
            data: form_data,
            processData: false,
            contentType: false,
            success: function (result) {
                _callback(result);
            },
            error: function (result) {
                _callback(result);
            }
        });
    }

    /**
     * 加载
     */
    jQuery.loading = function (_params, _callback) {
        var ajax_type     = this.isset(_params.type, "post"),
            ajax_url      = this.isset(_params.url, "?ajax_url=undefined"),
            ajax_data     = this.isset(_params.data, {}),
            ajax_async    = this.isset(_params.async, true),
            ajax_cache    = this.isset(_params.cache, false),
            ajax_dataType = this.isset(_params.dataType, ""),
            ajax_processData = this.isset(_params.processData, true);
        jQuery.ajax({
            type: ajax_type,
            async: ajax_async,
            cache: ajax_cache,
            dataType: ajax_dataType,
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
    jQuery.scrollTop = function (_element) {
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
     * 变量是否存在
     */
    jQuery.isset = function (_name, _default) {
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
     * 是否选中
     */
    jQuery.isChecked = function(_element) {
        return jQuery(_element).is(":checked");
    };

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
     * URL get参数
     */
    jQuery.getParam = function (_key) {
        var reg = new RegExp("(^|&)" + _key + "=([^&]*)(&|$)");
        var result = window.location.search.substr(1).match(reg);
        return result ? decodeURIComponent(result[2]) : null;
    }

    /**
     * 转换JSON数据
     */
    jQuery.parseJSON = function (_result) {
        return eval("(" + _result + ")");
    }

    /**
     * 错误
     * @param string _msg  信息
     * @param mixed  _data 输出数据
     */
    jQuery.printError = function (_msg, _data)
    {
        console.group('Error');
        console.error(_msg);
        for (var index in _data) {
            console.log(index+': ', _data[index]);
        }
        console.groupEnd();
    }

}));