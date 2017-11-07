<?php
/**
 *
 * 自定义字段 - 验证器
 *
 * @package   NiPHPCMS
 * @category  common\validate
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\validate;

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
        'category_id.require'    => '{%error category require}',
        'category_id.number'     => '{%error category number}',
        'type_id.require'        => '{%error type require}',
        'type_id.number'         => '{%error type number}',
        'name.require'           => '{%error name require}',
        'name.length'            => '{%error name length not}',
        'description.max'        => '{%error description length not}',
        'is_require.require'     => '{%error isrequire require}',
        'is_require.number'      => '{%error isrequire number}',
    ];

    protected $scene = [
        'create' => [
            'category_id',
            'type_id',
            'name',
            'description',
            'is_require',
        ],
        'update' => [
            'id',
            'name',
            'description',
            'is_require',
        ],
        'illegal' => ['id'],
        'remove' => ['id'],
    ];
}
