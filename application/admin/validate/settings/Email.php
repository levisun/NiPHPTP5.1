<?php
/**
 *
 * 邮箱设置 - 设置 - 验证器
 *
 * @package   NiPHPCMS
 * @category  application\admin\validate\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\settings;

use think\Validate;

class Email extends Validate
{
    protected $rule = [
        'smtp_host'       => ['require', 'token'],
        'smtp_port'       => ['require', 'number'],
        'smtp_username'   => ['require'],
        'smtp_password'   => ['require'],
        'smtp_from_email' => ['require'],
        'smtp_from_name'  => ['require'],
    ];

    protected $message = [
        'smtp_host.require'       => '{%error emailsms smtp host}',
        'smtp_port.require'       => '{%error emailsms smtp port}',
        'smtp_port.number'        => '{%error emailsms smtp port}',
        'smtp_username.require'   => '{%error emailsms smtp username}',
        'smtp_password.require'   => '{%error emailsms smtp password}',
        'smtp_from_email.require' => '{%error emailsms smtp from email}',
        'smtp_from_name.require'  => '{%error emailsms smtp from name}',
    ];
}
