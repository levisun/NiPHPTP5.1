<?php
/**
 *
 * 新浪微博登陆Api
 *
 * @package   NiPHPCMS
 * @category  net\oauth\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2016/12
 */
namespace oauth;

use oauth\Base;
use oauth\OAuthHttp;

class Weixin extends Base
{
    protected $AuthorizeURL   = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    protected $AccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    protected $ApiBase        = 'https://api.weixin.qq.com/sns/';

    public function getAuthorizeURL()
    {
        setcookie('A_S', $this->timestamp);
        $_COOKIE['A_S'] = $this->timestamp;
        $this->initConfig();
        //Oauth 标准参数
        $params = [
            'appid'         => $this->config['app_key'],
            'redirect_uri'  => $this->config['callback'],
            'response_type' => $this->config['response_type'],
            'scope'         => $this->config['scope'],
            'state'         => $this->timestamp,
        ];
        return $this->AuthorizeURL . '?' . http_build_query($params);
    }

    /**
     * 默认的AccessToken请求参数
     * @return type
     */
    protected function _params()
    {
        $params = [
            'appid'      => $this->config['app_key'],
            'secret'     => $this->config['app_secret'],
            'grant_type' => $this->config['grant_type'],
            'code'       => $_GET['code'],
        ];
        return $params;
    }

    public function call($api, $param='', $method='GET')
    {
        /* 调用公共参数 */
        $params = [
            'access_token' => $this->token['access_token'],
            'openid'       => $this->openid(),
            'lang'         => 'zh_CN'
        ];

        $data = OAuthHttp::request($this->url($api), $params, $method);
        return json_decode($data, true);
    }

    public function parseToken($result)
    {
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in'] && $data['openid']) {
            return $data;
        } else {
            $this->error[] = '获取微信 ACCESS_TOKEN 出错：' . $result;
            return false;
        }
    }

    public function openid()
    {
        $data = $this->token;
        if (isset($data['openid'])) {
            return $data['openid'];
        } else {
            $this->error[] = '没有获取到 openid！';
            return false;
        }
    }

    public function userinfo()
    {
        $rsp = $this->call('userinfo');
        if (!$rsp || (isset($rsp['errcode']) && $rsp['errcode'] != 0)) {
            $this->error[] = '接口访问失败！' . $rsp['error'];
            return false;
        } else {
            $userinfo = [
                'openid'  => $this->token['openid'],
                'unionid' => isset($this->token['unionid']) ? $this->token['unionid'] : '',
                'channel' => 'weixin',
                'nick'    => $rsp['nickname'],
                'gender'  => $rsp['sex'] == 1 ? 'm' : 'f'
            ];
            return $userinfo;
        }
    }

    public function userinfo_all()
    {
        $rsp = $this->call('userinfo');
        if (!$rsp || (isset($rsp['errcode']) && $rsp['errcode'] != 0)) {
            $this->error[] = '接口访问失败！' . $rsp['errmsg'];
            return false;
        } else {
            return $rsp;
        }
    }
}
