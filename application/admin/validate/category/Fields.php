<?php
/**
 *
 * 自定义字段 - 栏目 - 验证器
 *
 * @package   NiPHPCMS
 * @category  admin\validate\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/02
 */
namespace app\admin\validate\category;

use think\Validate;

class Fields extends Validate
{
    protected $rule = [
        'id'          => ['require', 'number'],
        'category_id' => ['require', 'number'],
        'type_id'     => ['require', 'number'],
        'name'        => ['require', 'length:2,255', 'token'],
        'description' => ['max:500'],
        'is_require'  => ['require', 'number'],
    ];

    protected $message = [
        'id.require'             => '{%illegal operation}',
        'id.number'              => '{%illegal operation}',
        'category_id.require'    => '{%error fields category require array}',
        'category_id.number'     => '{%error fields category require array}',
        'type_id.require'        => '{%error fields type require number}',
        'type_id.number'         => '{%error fields type require number}',
        'name.require'           => '{%error fields name require}',
        'name.length'            => '{%error catname length not}',
        'description.max'        => '{%error aliases length not}',
        'is_require.require'     => '{%error isrequire require number}',
        'is_require.number'      => '{%error isrequire require number}',
    ];

    protected $scene = [
        'added' => [
            'name',
            'category_id',
            'type_id',
            'description',
            'is_require',
        ],
        'editor' => [
            'id',
            'name',
            'category_id',
            'type_id',
            'description',
            'is_require',
        ],
    ];
}
