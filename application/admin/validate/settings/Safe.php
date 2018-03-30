<?php
/**
 *
 * 安全与效率设置 - 设置 - 验证器
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

class Safe extends Validate
{
    protected $rule = [
        'content_check'          => ['require', 'token'],
        'member_login_captcha'   => ['require', 'number'],
        'website_submit_captcha' => ['require', 'number'],
        'website_static'         => ['require'],
        'upload_file_max'        => ['require', 'number'],
        'upload_file_type'       => ['require'],
    ];

    protected $message = [
        'content_check'          => '{%error safe content check}',
        'member_login_captcha'   => '{%error safe member login captcha}',
        'website_submit_captcha' => '{%error safe website submit captcha}',
        'website_static'         => '{%error safe website static}',
        'upload_file_max'        => '{%error safe upload file max}',
        'upload_file_type'       => '{%error safe upload file type}',
    ];
}
