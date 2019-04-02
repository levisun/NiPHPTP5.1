<?php
/**
 *
 * 数据层
 * 地区表
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

class Region extends Model
{
    protected $name = 'region';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'pid' => 'integer',
    ];
    protected $field = [
        'id',
        'pid',
        'name'
    ];
}
