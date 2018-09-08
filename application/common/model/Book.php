<?php
/**
 *
 * 书库表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class Book extends Model
{
    use SoftDelete;
    protected $name = 'book';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'type_id' => 'integer',
        'user_id' => 'integer',
        'is_show' => 'integer',
        'sort'    => 'integer',
        'hits'    => 'integer',
    ];
    protected $field = [
        'id',
        'name',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'image',
        'type_id',
        'user_id',
        'is_show',
        'sort',
        'hits',
        'update_time',
        'delete_time',
        'create_time',
        'lang'
    ];
}
