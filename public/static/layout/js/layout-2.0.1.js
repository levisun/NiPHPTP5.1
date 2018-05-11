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

            jQuery(this).each(function(){
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
            jQuery(this).each(function(){
                var arr = {
                    self:       this,
                    name:       jQuery(this).attr("name"),
                    id:         jQuery(this).attr("id"),
                    class:      jQuery(this).attr("class"),
                    src:        jQuery(this).attr("src"),
                    title:      jQuery(this).attr("title"),
                    href:       jQuery(this).attr("href"),

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
    jQuery.uiPopup = function (_params, _status = "init") {
        if (_status === "init") {
            var style = jQuery.isSet(_params.style, "");
            if (style) {
                style = "layoutUi-popup-"+style;
            }

            var html = "<div class='layoutUi-popup "+style+"'><div class='layoutUi-popup-mask'></div><div class='layoutUi-popup-container'><div class='layoutUi-pull-right layoutUi-close' style='margin: 10px' bindtap=''></div>";
            if (jQuery.isSet(_params.title)) {
                html += "<div class='layoutUi-popup-header'>"+_params.title+"</div>";
            }
            if (jQuery.isSet(_params.content)) {
                html += "<div class='layoutUi-popup-content'>"+_params.content+"</div>";
            }
            if (jQuery.isSet(_params.footer)) {
                html += "<div class='layoutUi-popup-footer'>"+_params.footer+"</div>";
            }
            html += "</div></div>";
            html += "<script type='text/javascript'>$('.layoutUi-close').click(function(){$.uiPopup({}, 'hide');});$('.layoutUi-popup-mask').click(function(){$.uiPopup({}, 'hide');});$('"+_params.element+"').click(function(){$.uiPopup({}, 'show');});</script>";
            jQuery("body").append(html);
        } else if (_status === "show") {
            jQuery(".layoutUi-popup").addClass("layoutUi-popup-show");
        } else if (_status === "hide") {
            jQuery(".layoutUi-popup").removeClass("layoutUi-popup-show");
        }
    }

    /**
     * 加载弹框提示
     */
    jQuery.uiLoadpopup = function (_tips = "加载中...", _element = "body") {
        if (_tips === false) {
            jQuery("div.layoutUi-loadpopup").remove();
            jQuery("body").removeAttr("style");
            clearTimeout(st);
        } else {
            var html = "";
            jQuery("body").css({"height": "100%", "overflow": "hidden"});
            html  = "<div class='layoutUi-loadpopup'>";
            html += "<div class='layoutUi-loadpopup-mask'></div>";
            html += "<div class='layoutUi-loadpopup-tips'>";
            html += "<div class='layoutUi-loadpopup-loading'></div>"+_tips;
            html += "</div>";
            html += "</div>";
            jQuery(_element).append(html);

            var st = setTimeout(function(){
                jQuery.uiLoadpopup(false);
            }, 15 * 1000);
        }
    }

    /**
     * 轻加载提示
     */
    jQuery.uiLoadmore = function (_tips = "加载中...", _element = "body") {
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
    jQuery.uiToast = function (_tips, _time = 1.5) {
        var html = "<div class='layoutUi-toast-mask'></div><div class='layoutUi-toast-tips'>"+_tips+"</div>";
        $("body").append(html);

        setTimeout(function(){
            $("div.layoutUi-toast-mask").remove();
            $("div.layoutUi-toast-tips").remove();
        }, _time * 1000);
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
     * 上传
     */
    jQuery.upload = function (_params, _callback) {
        jQuery.uiLoadpopup();

        _params.type = jQuery.isSet(_params.type, "post");

        var form_data = new FormData(_params.file);

        jQuery.ajax({
            url:         _params.url,
            type:        _params.type,
            cache:       false,
            data:        form_data,
            processData: false,
            contentType: false,
            success: function (result) {
                _callback(result);
                jQuery.uiLoadpopup(false);
            },
            error: function (result) {
                _callback(result);
                jQuery.uiLoadpopup(false);
            }
        });
    }

    /**
     * 加载更多
     */
    jQuery.loadMore = function (_callback) {
        jQuery("body").attr("Layout-loading-page", 1);
        jQuery("body").attr("Layout-loading-bool", "true");

        jQuery(window).scroll(function(){
            var is = jQuery("body").attr("Layout-loading-bool");
            if (is == "true" && jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) / 1.05) {

                var page_num = jQuery("body").attr("Layout-loading-page");
                    page_num++;

                jQuery("body").attr("Layout-loading-page", page_num);
                jQuery("body").attr("Layout-loading-bool", "false");

                jQuery.loading({
                    type: "get",
                    url: window.location.href,
                    animation: true,
                    data: {
                        p: page_num,
                    },
                }, function (result) {
                    jQuery("body").attr("Layout-loading-bool", "true");
                    jQuery("body").removeAttr("style");

                    _callback(result);
                });
            }
        });
    }

    /**
     * 加载
     */
    jQuery.loading = function (_params, _callback) {
        if (jQuery.isSet(_params.animation) && _params.animation === true) {
            jQuery.uiLoadpopup();
        }

        var ajax_type        = jQuery.isSet(_params.type, "post"),
            ajax_url         = jQuery.isSet(_params.url, "?ajax_url=undefined"),
            ajax_data        = jQuery.isSet(_params.data, {}),
            ajax_async       = jQuery.isSet(_params.async, true),
            ajax_cache       = jQuery.isSet(_params.cache, false),
            ajax_dataType    = jQuery.isSet(_params.dataType, ""),
            ajax_processData = jQuery.isSet(_params.processData, true);

        jQuery.ajax({
            type:     ajax_type,
            async:    ajax_async,
            cache:    ajax_cache,
            dataType: ajax_dataType,
            url:      ajax_url,
            data:     ajax_data,
            success: function (result) {
                _callback(result);
                if (jQuery.isSet(_params.animation) && _params.animation === true) {
                    jQuery.uiLoadpopup(false);
                }
            },
            error: function (result) {
                _callback(result);
                if (jQuery.isSet(_params.animation) && _params.animation === true) {
                    jQuery.uiLoadpopup(false);
                }
            }
        });
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
            jQuery("body,html").animate({scrollTop: 0}, 300);
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
    jQuery.isSet = function (_name, _default) {
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
     * 重写URL地址[不刷新]
     */
    jQuery.rewriteUrl = function(_url) {
        history.replaceState(null, null, _url);
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
    jQuery.urlParam = function (_key, _default = "", _url = "") {
        var reg = new RegExp("(^|&)" + _key + "=([^&]*)(&|$)");
        var value = "";
        if (_url) {
            var array = this.explode("?", _url);
            _url = array[1] ? array[1] : "";
        } else {
            _url = window.location.search.substr(1);
        }

        var result = _url.match(reg);

        if (result) {
            value = decodeURIComponent(result[2]);
        }

        return value ? value : _default;
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
