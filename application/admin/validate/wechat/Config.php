<?php
/**
 *
 * 接口设置 - 微信 - 验证器
 *
 * @package   NiPHPCMS
 * @category  admin\validate\wechat
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\wechat;

use think\Validate;

class Config extends Validate
{
    protected $rule = [
        'wechat_token'          => ['require'],
        'wechat_encodingaeskey' => ['require'],
        'wechat_appid'          => ['require'],
        'wechat_appsecret'      => ['require'],
    ];

    protected $message = [
        'wechat_token.require'          => '{%error wechattoken require}',
        'wechat_encodingaeskey.require' => '{%error wechatencodingaeskey require}',
        'wechat_appid.require'          => '{%error wechatappid require}',
        'wechat_appsecret.require'      => '{%error wechatappsecret require}',
    ];
}
