<?php
/**
 *
 * 数据层
 * 栏目表
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

class Category extends Model
{
    protected $name = 'category';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $type = [
        'type_id'    => 'integer',
        'model_id'   => 'integer',
        'is_show'    => 'integer',
        'is_channel' => 'integer',
        'sort_order' => 'integer',
        'access_id'  => 'integer',
    ];
    protected $field = [
        'id',
        'pid',
        'name',
        'aliases',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'image',
        'type_id',
        'model_id',
        'is_show',
        'is_channel',
        'sort_order',
        'access_id',
        'url',
        'create_time',
        'update_time',
        'lang'
    ];
}
