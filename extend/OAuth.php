<?php
/**
 *
 */
/*
$config = [
    'app_key'    => '101246655',
    'app_secret' => '55f8f17f4b83f9b99c3d962f77b2a156',
    'scope'      => 'get_user_info',
    'callback'   => [
        'default' => $callback,
        'mobile'  => $callback,
    ]
];
*/

use \oauth\qq;
use \oauth\weibo;
use \oauth\weixin;
use \oauth\wxqrcode;

class OAuth
{
    private $config = [];

    public function __construct($_config)
    {
        $this->config = [
            'app_key'    => $_config['app_key'],
            'app_secret' => $_config['app_secret'],
            'scope'      => !empty($_config['scope']) ? $_config['scope'] : 'get_user_info',
            'callback'   => $_config['callback'],
        ];
    }

    /**
     * 请求回调
     * @access private
     * @param
     * @return array
     */
    private function callback($_type, $_is_mobile = false)
    {
        if ($_is_mobile) {
            $display = 'mobile';
        } else {
            $display = 'default';
        }

        $class = $_type;

        $oauth = new $class($this->config, $display);
        $oauth->getAccessToken();
        $user_info = $oauth->userinfo();
        if (!$user_info) {
            halt($oauth->error);
        }
    }

    /**
     * 请求Authorize访问地址
     * @access private
     * @param
     * @return array
     */
    private function authorizeURL($_type, $_is_mobile = false)
    {
        if ($_is_mobile) {
            $display = 'mobile';
        } else {
            $display = 'default';
        }

        $class = $_type;

        $oauth = new $class($this->config, $display);
        return $oauth->getAuthorizeURL();
    }
}
