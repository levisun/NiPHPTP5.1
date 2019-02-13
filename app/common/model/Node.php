<?php
/**
 *
 * 节点表 - 数据层
 *
 * @package   NiPHP
 * @category  app\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\common\model;

use think\Model;

class Node extends Model
{
    protected $name = 'node';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'status' => 'integer',
        'sort'   => 'integer',
        'pid'    => 'integer',
        'level'  => 'integer',
    ];
    protected $field = [
        'id',
        'name',
        'title',
        'status',
        'remark',
        'sort',
        'pid',
        'level',
    ];
}