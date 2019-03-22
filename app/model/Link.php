<?php
/**
 *
 * 数据层
 * 友情链接表
 *
 * @package   NiPHP
 * @category  ap\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

class Link extends Model
{
    use SoftDelete;
    protected $name = 'link';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'category_id' => 'integer',
        'type_id'     => 'integer',
        'admin_id'    => 'integer',
        'user_id'     => 'integer',
        'is_pass'     => 'integer',
        'hits'        => 'integer',
    ];
    protected $field = [
        'id',
        'title',
        'logo',
        'url',
        'remark',
        'category_id',
        'type_id',
        'admin_id',
        'user_id',
        'is_pass',
        'hits',
        'sort_order',
        'update_time',
        'delete_time',
        'create_time',
        'lang',
    ];
}