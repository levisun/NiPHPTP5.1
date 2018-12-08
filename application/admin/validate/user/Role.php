<?php
/**
 *
 * 管理员组 - 用户 - 验证器
 *
 * @package   NiPHP
 * @category  application\admin\validate\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\user;

use think\Validate;

class Role extends Validate
{

    protected $rule = [
        'id'     => ['require', 'number'],
        'name'   => ['require', 'length:2,20', 'unique:role', 'token'],
        'status' => ['require', 'number'],
        'remark' => ['max:250'],
    ];

    protected $message = [
        'id.require'     => '{%illegal operation}',
        'id.number'      => '{%illegal operation}',
        'name.require'   => '{%error rolename require}',
        'name.length'    => '{%error rolename length not}',
        'name.unique'    => '{%error rolename unique}',
        'status.require' => '{%error status require}',
        'status.number'  => '{%error status number}',
        'remark.max'     => '{%error remark length not}',
    ];

    protected $scene = [
        'added' => [
            'name',
            'status',
            'remark'
        ],
        'editor' => [
            'id',
            'name',
            'status',
            'remark'
        ],
    ];
}
