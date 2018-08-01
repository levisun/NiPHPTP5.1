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

use \oauth\QQ;
use \oauth\Weibo;
use \oauth\Weixin;
use \oauth\Wxqrcode;

function oauth($_config, $_type, $_mobile = false)
{
    $display = $_mobile ? 'mobile' : 'default';

    $class = $_type;

    $oauth = new $class($t_config, $display);
    return $oauth->getAuthorizeURL();
}

function oauth_callback($_config, $_type, $_mobile = false)
{
    $display = $_mobile ? 'mobile' : 'default';

    $class = $_type;

    $oauth = new $class($_config, $display);
    $oauth->getAccessToken();
    $user_info = $oauth->userinfo();
    if (!$user_info) {
        halt($oauth->error);
    }
}
