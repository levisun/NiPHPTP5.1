<?php
/**
 *
 * SESSION表 - 数据层
 *
 * @package   NiPHP
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019/01
 */
namespace app\common\model;

use think\Model;

class Session extends Model
{
    protected $name = 'session';
    protected $autoWriteTimestamp = false;
    protected $updateTime = 'update_time';
    protected $pk = 'session_id';
    protected $type = [
        // 'count' => 'integer',
    ];
    protected $field = [
        'session_id',
        'data',
        'update_time',
    ];
}
