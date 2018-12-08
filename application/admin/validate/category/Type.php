<?php
/**
 *
 * 类别 - 栏目 - 验证器
 *
 * @package   NiPHP
 * @category  application\admin\validate\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/02
 */
namespace app\admin\validate\category;

use think\Validate;

class Type extends Validate
{
    protected $rule = [
        'id'          => ['require', 'number'],
        'name'        => ['require', 'length:2,255', 'token'],
        'category_id' => ['require', 'number'],
        'description' => ['max:500'],
    ];

    protected $message = [
        'id.require'          => '{%illegal operation}',
        'id.number'           => '{%illegal operation}',
        'name.require'        => '{%error type name require}',
        'name.length'         => '{%error type name length not}',
        'category_id.require' => '{%error category_id require}',
        'category_id.number'  => '{%error category_id number}',
        'description.max'     => '{%error description length not}',
    ];

    protected $scene = [
        'added' => [
            'name',
            'category_id',
            'description'
        ],
        'editor' => [
            'id',
            'name',
            'description'
        ],
    ];
}
