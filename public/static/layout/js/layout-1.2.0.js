
export default class
{
    version: '1.2.0';
    debug: false;

    constructor(_debug)
    {
        this.debug = _debug;
    }

    /**
     * 回到顶部
     */
    scrollTop(element)
    {
        // 监听滚动
        jQuery(window).scroll(function () {
            if (jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) / 2) {
                jQuery(element).fadeIn(1000);
            } else {
                jQuery(element).fadeOut(500);
            }
        });

        // 点击回到顶部
        jQuery(element).click(function(){
            jQuery("body,html").animate({scrollTop: 0}, 300);
        });

        jQuery("body").append("<div></div>");
    }

    /**
     * 判断是否微信端访问
     */
    isWechat()
    {
        let reg = /(MicroMessenger)/i;
        let user_agent = navigator.userAgent.toLowerCase();
        let result = user_agent.match(reg);
        if (result == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 判断是否移动端访问
     */
    isMobile()
    {
        var reg = /(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i;
        var user_agent = navigator.userAgent.toLowerCase();
        var result = user_agent.match(reg);
        if (result == null) {
            return false;
        } else {
            return true;
        }
    }
}
