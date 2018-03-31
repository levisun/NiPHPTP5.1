<?php
/**
 * 微信API接口
 *
 * @package   NiPHPCMS
 * @category  extend
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

use \Wechat;

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
            'type'      => $this->getRev()->getRevType(),
            'event'     => $this->getRevEvent(),
            'form_user' => $this->getRevFrom(),
            'user_data' => $this->getUserInfo($this->getRevFrom()),
            'key'       => [
                // 扫公众号二维码返回值
                'sceneId'       => $this->getRevSceneId(),
                // 获得的地理信息
                'eventLocation' => $this->getRevEventGeo(),
                // 文字信息
                'text'          => $this->getRevContent(),
                // 图片信息
                'image'         => $this->getRevPic(),
                // 地理信息
                'location'      => $this->getRevGeo(),
                // 链接信息
                'link'          => $this->getRevLink(),
                // 音频信息
                'voice'         => $this->getRevVoice(),
                // 视频信息
                'video'         => $this->getRevVideo(),
                // 群发或模板信息回复内容
                'result'        => $this->getRevResult(),
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
        if (empty($_COOKIE['wechat_openid'])) {
            // 网页授权获得用户openid后再获得用户信息
            if (!empty($_GET['code'])) {
                $code  = $_GET['code'];
                $state = $_GET['state'];
                if ($state == 'wechatOauth') {
                    // 通过code获得openid
                    $result = $this->getOauthAccessToken($code);
                    setcookie('wechat_openid', $result['openid']);
                    $_COOKIE['wechat_openid'] = $result['openid'];
                }
            } else {
                // 直接跳转不授权获取code
                $url = request()->url();
                $url = $this->getOauthRedirect($url, 'wechatOauth', 'snsapi_base');
                header('Location:' . $url);
                exit();
            }
        }
    }

    /**
     * 获取JsApi使用签名
     * @access public
     * @param
     * @return mixed
     */
    public function jsSign($_debug = 'false', $_version = '1.2.0')
    {
        $result = parent::getJsSign(request()->url());

        $code = [
            'wechat_js_sign' => $result,
            'wecaht_js_code' => '<script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-' . $_version . '.js"></script><script type="text/javascript">wx.config({debug: '. $_debug . ',appId: "' . $result['appId'] . '",timestamp: ' . $result['timestamp'] . ',nonceStr: "' . $result['nonceStr'] . '",signature: "' . $result['signature'] . '",jsApiList: ["checkJsApi","onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone","hideMenuItems","showMenuItems","hideAllNonBaseMenuItem","showAllNonBaseMenuItem","translateVoice","startRecord","stopRecord","onVoiceRecordEnd","playVoice","onVoicePlayEnd","pauseVoice","stopVoice","uploadVoice","downloadVoice","chooseImage","previewImage","uploadImage","downloadImage","getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","closeWindow","scanQRCode","chooseWXPay","openProductSpecificView","addCard","chooseCard","openCard","openAddress"]});</script>'
        ];

        if ($_debug === 'true') {
            $code['wecaht_js_code'] .= 'wx.error(function (res) {alert(res.errMsg);});';
        }

        return $code;
    }

    /**
     * 设置缓存，按需重载
     * @access protected
     * @param  string  $_cachename
     * @param  mixed   $_value
     * @param  int     $_expired
     * @return boolean
     */
    protected function setCache($_cachename, $_value, $_expired = 7100)
    {
        // cache('_cachename', $_value, $_expired);
        $dir  = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $dir .= 'runtime' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        $file = $dir . $_cachename . '.php';

        $data = [
            'value' => $_value,
            'time'  => time() + $_expired,
            ];

        file_put_contents($file, '<?php $cache=' . var_export($data, true) . ';?>', true);

        return true;
    }

    /**
     * 获取缓存，按需重载
     * @access protected
     * @param  string $_cachename
     * @return mixed
     */
    protected function getCache($_cachename)
    {
        // cache($_cachename);
        $dir  = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $dir .= 'runtime' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        $file = $dir . $_cachename . '.php';

        if (is_file($file)) {
            include $file;
            if (!empty($cache) && $cache['time'] >= time()) {
                $return $cache['value'];
            } else {
                $return false;
            }
        } else {
            $return false;
        }

        return $return;
    }

    /**
     * 清除缓存，按需重载
     * @access protected
     * @param  string $_cachename
     * @return boolean
     */
    protected function removeCache($_cachename)
    {
        // cache($_cachename, null);
        $dir  = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $dir .= 'runtime' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        $file = $dir . $_cachename . '.php';

        unlink($file);
        return false;
    }
}
