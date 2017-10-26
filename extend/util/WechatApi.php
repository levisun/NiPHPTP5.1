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
    public $receive;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * AIP请求
     * @access public
     * @param  boolean $_valid
     * @return array
     */
    public function apiRequest($_valid = true)
    {
        if ($_valid) {
            $this->valid();
        }

        $this->receive = [
            'type'     => $this->getRev()->getRevType(),
            'event'    => $this->getRevEvent(),
            'form_user' => $this->getRevFrom(),
            'user_data' => $this->getUserInfo($this->getRevFrom()),
            'key'      => [
                'sceneId'       => escape_xss($this->getRevSceneId()),      // 扫公众号二维码返回值
                'eventLocation' => escape_xss($this->getRevEventGeo()),     // 获得的地理信息
                'text'          => escape_xss($this->getRevContent()),      // 文字信息
                'image'         => escape_xss($this->getRevPic()),          // 图片信息
                'location'      => escape_xss($this->getRevGeo()),          // 地理信息
                'link'          => escape_xss($this->getRevLink()),         // 链接信息
                'voice'         => escape_xss($this->getRevVoice()),        // 音频信息
                'video'         => escape_xss($this->getRevVideo()),        // 视频信息
                'result'        => escape_xss($this->getRevResult()),       // 群发或模板信息回复内容
            ],
        ];

        return $this->receive;
    }

    /**
     * AIP回复
     * @access public
     * @param
     * @return void
     */
    public function apiReply()
    {
        switch ($this->receive['type']) {
            // 文字信息
            case Wechat::MSGTYPE_TEXT:
                $return = [
                    'type' => 'text',
                    'data' => $this->receive['key']['text'],
                ];
                break;

            // 图片信息
            case Wechat::MSGTYPE_IMAGE:
                $return = [
                    'type' => 'image',
                    'data' => $this->receive['key']['image'],
                ];
                break;

            // 地址信息
            case Wechat::MSGTYPE_LOCATION:
                $return = [
                    'type' => 'location',
                    'data' => $this->receive['key']['location'],
                ];
                break;

            // 链接信息
            case Wechat::MSGTYPE_LINK:
                $return = [
                    'type' => 'link',
                    'data' => $this->receive['key']['link'],
                ];
                break;

            // 音频信息
            case Wechat::MSGTYPE_VOICE:
                $return = [
                    'type' => 'voice',
                    'data' => $this->receive['key']['voice'],
                ];
                break;

            // 视频信息
            case Wechat::MSGTYPE_VIDEO:
            case Wechat::MSGTYPE_SHORTVIDEO:
                $return = [
                    'type' => 'video',
                    'data' => $this->receive['key']['video'],
                ];
                break;

            // 音乐信息
            case Wechat::MSGTYPE_MUSIC:
                $return = [
                    'type' => 'music',
                    'data' => '',
                ];
                break;

            // 图文信息
            case Wechat::MSGTYPE_NEWS:
                $return = [
                    'type' => 'news',
                    'data' => '',
                ];
                break;

            // 事件推送信息
            case Wechat::MSGTYPE_EVENT:
                $return = [
                    'type' => 'event',
                    'data' => $this->event(),
                ];
                break;

            default:
                $return = [
                    'type' => 'text',
                    'data' => $this->receive['key']['text'],
                ];
                break;
        }

        return $return;
    }

    /**
     * 事件推送信息
     * @access protected
     * @param
     * @return array
     */
    protected function event()
    {
        $return = [];

        // 关注事件
        if ($this->receive['event']['event'] == Wechat::EVENT_SUBSCRIBE) {
            $return['event_type'] = 'subscribe';

            // 获取二维码的场景值
            if ($this->receive['key']['sceneId']) {
                $return['sceneId'] = $this->receive['key']['sceneId'];
            }
        }

        // 取消关注事件
        if ($this->receive['event']['event'] == Wechat::EVENT_UNSUBSCRIBE) {
            $return['event_type'] = 'unsubscribe';
        }

        // 上报地理位置事件
        if ($this->receive['event']['event'] == Wechat::EVENT_LOCATION) {
            $return['event_type'] = 'location';
            $return['location'] = $this->receive['key']['eventLocation'];
        }

        // 点击菜单跳转链接
        if ($this->receive['event']['event'] == Wechat::EVENT_MENU_VIEW) {
            $return['event_type'] = 'menu_view';
        }

        // 点击菜单拉取消息
        if ($this->receive['event']['event'] == Wechat::EVENT_MENU_CLICK) {
            $return['event_type'] = 'menu_click';
        }

        // 模板消息发送结果
        if (
            $this->receive['event']['event'] == Wechat::EVENT_SEND_TEMPLATE ||
            $this->receive['event']['event'] == Wechat::EVENT_SEND_MASS
            ) {
            $result = $this->receive['key']['result'];
            if ($result !== false) {
                if ($result['Status'] != 'success') {

                }
            }
        }

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
            if (input('?param.code')) {
                $code  = input('param.code');
                $state = input('param.state');
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
     * @access protected
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
     * @access protected
     * @param  string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        cache($cachename);
    }

    /**
     * 清除缓存，按需重载
     * @access protected
     * @param  string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        cache($cachename, null);
    }
}
