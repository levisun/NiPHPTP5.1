<?php
/**
 *
 * 数据层
 * 文章表
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
use think\model\concern\SoftDelete;

class Article extends Model
{
    use SoftDelete;
    protected $name = 'article';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'category_id' => 'integer',
        'type_id'     => 'integer',
        'is_pass'     => 'integer',
        'is_com'      => 'integer',
        'is_top'      => 'integer',
        'is_hot'      => 'integer',
        'sort_order'  => 'integer',
        'hits'        => 'integer',
        'admin_id'    => 'integer',
        'user_id'     => 'integer',
    ];
    protected $field = [
        'id',
        'title',
        'keywords',
        'description',
        'category_id',
        'type_id',
        'is_pass',
        'is_com',
        'is_top',
        'is_hot',
        'sort_order',
        'hits',
        'username',
        'origin',
        'admin_id',
        'user_id',
        'show_time',
        'create_time',
        'update_time',
        'delete_time',
        'access_id',
        'lang'
    ];
}
