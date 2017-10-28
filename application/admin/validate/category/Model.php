<?php
/**
 *
 * 管理模型 - 栏目 - 验证器
 *
 * @package   NiPHPCMS
 * @category  admin\validate\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Model.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\validate\category;

use think\Validate;

class Model extends Validate
{
    protected $rule = [
        'id'          => ['require', 'number'],
        'name'        => ['require', 'length:4,255', 'unique:model', 'token'],
        'table_name'  => ['require', 'length:4,255', 'alpha'],
        'remark'      => ['max:255'],
        'status'      => ['number'],
        'model_table' => ['require', 'alpha']
    ];

    protected $message = [
        'id.require'          => '{%illegal operation}',
        'id.number'           => '{%illegal operation}',
        'name.require'        => '{%error model name require}',
        'name.length'         => '{%error model name length not}',
        'name.unique'         => '{%error model name unique}',
        'table_name.require'  => '{%error table name require}',
        'table_name.length'   => '{%error table name length not}',
        'table_name.unique'   => '{%error table name unique}',
        'table_name.alpha'    => '{%error table name alpha}',
        'model_table.require' => '{%error model table require}',
        'model_table.alpha'   => '{%error model table alpha}',
        'remark.max'          => '{%error remark length not}',
        'status.number'       => '{%error status number}',
    ];

    protected $scene = [
        'create' => [
            'name',
            'table_name',
            'remark',
            'status',
            'model_table'
        ],
        'editor' => [
            'id',
            'name',
            'remark',
            'status',
        ],
    ];
}
