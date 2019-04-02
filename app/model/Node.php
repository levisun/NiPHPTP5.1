<?php
/**
 *
 * 数据层
 * 节点表
 *
 * @package   NICMS
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;

class Node extends Model
{
    protected $name = 'node';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'status'     => 'integer',
        'sort_order' => 'integer',
        'pid'        => 'integer',
        'level'      => 'integer',
    ];
    protected $field = [
        'id',
        'name',
        'title',
        'status',
        'remark',
        'sort_order',
        'pid',
        'level',
    ];
}
