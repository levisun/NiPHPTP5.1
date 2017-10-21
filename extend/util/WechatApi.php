<?php
/**
 * 微信API接口
 *
 * @package   NiPHPCMS
 * @category  extend\util\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: WechatApi.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/10/20
 */
namespace util;

use util\Wechat;

class WechatApi extends Wechat
{

    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * AIP请求
     * @access public
     * @param
     * @return void
     */
    public function apiRequest()
    {
        $this->valid();

        $return = [
            'type'     => $this->getRev()->getRevType(),
            'event'    => $this->getRevEvent(),
            'formUser' => $this->getRevFrom(),
            'userData' => $this->getUserInfo($this->getRevFrom()),
            'key'      => [
                'sceneId'       => escape_xss($this->getRevSceneId()),      // 扫公众号二维码返回值
                'eventLocation' => escape_xss($this->getRevEventGeo()),     // 获得的地理信息
                'text'          => escape_xss($this->getRevContent()),      // 文字信息
                'image'         => escape_xss($this->getRevPic()),          // 图片信息
                'location'      => escape_xss($this->getRevGeo()),          // 地理信息
                'link'          => escape_xss($this->getRevLink()),         // 链接信息
                'voice'         => escape_xss($this->getRevVoice()),        // 音频信息
                'video'         => escape_xss($this->getRevVideo()),        // 视频信息
                'result'        => escape_xss($this->getRevResult())        // 群发或模板信息回复内容
            ],
        ];

        return $return;
    }

    /**
     * 查询微信用户openid
     * 生成openid cookie
     * @access public
     * @param
     * @return boolean
     */
    public function getOpenId()
    {
        if (!cookie('?wechat_openid')) {
            // 网页授权获得用户openid后再获得用户信息
            if (request()->has('code', 'param')) {
                $code  = request()->param('code');
                $state = request()->param('state');
                if ($state == 'wechatOauth') {
                    // 通过code获得openid
                    $result = $this->getOauthAccessToken($code);
                    cookie('wechat_openid', $result['openid']);
                }
            } else {
                // 直接跳转不授权获取code
                $url = request()->url(true);
                $url = $this->getOauthRedirect($url, 'wechatOauth', 'snsapi_base');
                redirect($url);
            }
        }
    }

    /**
     * 获取JsApi使用签名
     * @access public
     * @param
     * @return mixed
     */
    public function jsSign($debug = 'false')
    {
        $result = parent::getJsSign(request()->url(true));

        $code = [
            'wechat_js_sign' => $result,
            'wecaht_js_code' => 'wx.config({debug: '. $debug . ',appId: "' . $result['appId'] . '",timestamp: ' . $result['timestamp'] . ',nonceStr: "' . $result['nonceStr'] . '",signature: "' . $result['signature'] . '",jsApiList: ["checkJsApi","onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone","hideMenuItems","showMenuItems","hideAllNonBaseMenuItem","showAllNonBaseMenuItem","translateVoice","startRecord","stopRecord","onVoiceRecordEnd","playVoice","onVoicePlayEnd","pauseVoice","stopVoice","uploadVoice","downloadVoice","chooseImage","previewImage","uploadImage","downloadImage","getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","closeWindow","scanQRCode","chooseWXPay","openProductSpecificView","addCard","chooseCard","openCard"]});'
        ];

        if ($debug === 'true') {
            $code['wecaht_js_code'] .= 'wx.error(function (res) {alert(res.errMsg);});';
        }

        return $code;
    }

    /**
     * 设置缓存，按需重载
     * @param  string  $cachename
     * @param  mixed   $value
     * @param  int     $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        $expired = $expired ? $expired : 7100;
        cache('cachename', $value, $expired);
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        cache($cachename);
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        cache($cachename, null);
    }
}
