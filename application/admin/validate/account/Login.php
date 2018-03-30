<?php
/**
 *
 * 登录 - 账户 - 验证器
 *
 * @package   NiPHPCMS
 * @category  application\admin\validate\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\account;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username' => ['require', 'length:6,20', 'token'],
        'password' => ['require', 'max:30'],
        // 'captcha'  => ['require', 'length:6', 'captcha'],
    ];

    protected $message = [
        'username.require' => '{%error username require}',
        'username.length'  => '{%error username length not}',
        'password.require' => '{%error password require}',
        'password.length'  => '{%error password length not}',
        'captcha.require'  => '{%error captcha require}',
        'captcha.length'   => '{%error captcha length}',
        'captcha.captcha'  => '{%error captcha}',
    ];
}
