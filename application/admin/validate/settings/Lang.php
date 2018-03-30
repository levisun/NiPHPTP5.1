<?php
/**
 *
 * 语言设置 - 设置 - 验证器
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

class Lang extends Validate
{
    protected $rule = [
        'system'         => ['require', 'token'],
        'website'        => ['require'],
        'lang_switch_on' => ['require'],
    ];

    protected $message = [
        'system.require'         => '{%error system default lang}',
        'website.require'        => '{%error website default lang}',
        'lang_switch_on.require' => '{%error domain auto}',
    ];
}
