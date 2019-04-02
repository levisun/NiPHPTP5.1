<?php
/**
 *
 * 数据层
 * 搜索引擎
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

class Searchengine extends Model
{
    protected $name = 'searchengine';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    // protected $pk = 'id';
    protected $type = [
        'count' => 'integer',
    ];
    protected $field = [
        // 'id',
        'date',
        'name',
        'user_agent',
        'count',
    ];
}
