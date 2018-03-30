<?php
/**
 *
 * 系统节点 - 用户 - 验证器
 *
 * @package   NiPHPCMS
 * @category  application\admin\validate\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\validate\user;

use think\Validate;

class Node extends Validate
{
    protected $rule = [
        'id'     => ['require', 'number'],
        'name'   => ['require', 'length:2,20', 'alpha', 'unique:node', 'token'],
        'title'  => ['require', 'length:2,50', 'unique:node'],
        'status' => ['require', 'number'],
        'remark' => ['max:250'],
        'pid'    => ['require', 'number'],
        'level'  => ['require', 'number'],
    ];

    protected $message = [
        'id.require'     => 'illegal operation',
        'id.number'      => 'illegal operation',
        'name.require'   => 'error nodename require',
        'name.length'    => 'error nodename length not',
        'name.alpha'     => 'error nodename alpha not',
        'name.unique'    => 'error nodename unique',

        'title.require'  => 'error nodetitle require',
        'title.length'   => 'error nodetitle length not',
        'title.unique'   => 'error nodetitle unique',

        'status.require' => 'error status require',
        'status.number'  => 'error status number',
        'remark.max'     => 'error remark length not',
        'pid.require'    => 'error pid require',
        'pid.number'     => 'error pid number',
        'level.require'  => 'error level require',
        'level.number'   => 'error level number',
    ];

    protected $scene = [
        'added' => [
            'name',
            'title',
            'status',
            'remark',
            'pid',
            'level'
        ],
        'editor' => [
            'id',
            'name',
            'title',
            'status',
            'remark',
            'pid',
        ],
    ];
}
