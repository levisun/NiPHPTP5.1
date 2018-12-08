<?php
/**
 *
 * 基础设置 - 设置 - 验证器
 *
 * @package   NiPHP
 * @category  application\admin\validate\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\settings;

use think\Validate;

class Basic extends Validate
{
    protected $rule = [
        'website_name' => ['require', 'max:500', 'token'],
    ];

    protected $message = [
        'website_name.require' => '{%please enter website name}',
        'website_name.max'     => '{%website name length shall not exceed 500}',
    ];
}
