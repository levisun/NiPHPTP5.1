<?php
/**
 *
 * 数据层
 * 附加字段表
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

class Fields extends Model
{
    protected $name = 'fields';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'category_id' => 'integer',
        'type_id'     => 'integer',
        'is_require'  => 'integer'
    ];
    protected $field = [
        'id',
        'category_id',
        'type_id',
        'name',
        'description',
        'is_require',
    ];
}
