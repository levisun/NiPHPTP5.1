<?php
/**
 *
 * 数据层
 * 访问表
 *
 * @package   NiPHP
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;

class Visit extends Model
{
    protected $name = 'visit';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'date',
        'ip',
        'ip_attr',
        'user_agent',
        'count',
    ];
}
