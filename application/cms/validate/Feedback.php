<?php
/**
 *
 * 反馈 - 验证器
 *
 * @package   NiPHPCMS
 * @category  application\cms\validate
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\cms\validate;

use think\Validate;

class Feedback extends Validate
{
    protected $rule = [
        'title'       => ['require', 'length:6,255', 'token'],
        'username'    => ['require', 'max:20'],
        'content'     => ['require', 'max:500'],
        'category_id' => ['require', 'number'],
        'type_id'     => ['require', 'number'],
        'mebmer_id'   => ['require', 'number'],
        'is_pass'     => ['require', 'number'],
        'captcha'     => ['require', 'length:6', 'captcha'],
    ];

    protected $message = [
        'title.require'       => '{%error title require}',
        'title.length'        => '{%error title length not}',
        'username.require'    => '{%error username require}',
        'username.max'        => '{%error username length not}',
        'content.require'     => '{%error content require}',
        'content.max'         => '{%error content length not}',
        'category_id.require' => '{%error category_id require}',
        'category_id.number'  => '{%error category_id type not}',
        'type_id.require'     => '{%error type_id require}',
        'type_id.number'      => '{%error type_id type not}',
        'mebmer_id.require'   => '{%error mebmer_id require}',
        'mebmer_id.number'    => '{%error mebmer_id type not}',
        'captcha.require'     => '{%error captcha require}',
        'captcha.length'      => '{%error captcha length}',
        'captcha.captcha'     => '{%error captcha}',
    ];
}
