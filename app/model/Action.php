<?php
/**
 *
 * 行为表 - 数据层
 *
 * @package   NICMS
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\model;

use think\Model;

class Action extends Model
{
    protected $name = 'action';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'name',
        'title',
        'remark'
    ];
}
