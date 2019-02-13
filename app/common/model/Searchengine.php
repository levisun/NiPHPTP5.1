<?php
/**
 *
 * 搜索引擎 - 数据层
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

class Searchengine extends Model
{
    protected $name = 'searchengine';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
     protected $type = [
        'count' => 'integer',
    ];
    protected $field = [
        'id',
        'date',
        'name',
        'user_agent',
        'count',
    ];
}