<?php
/**
 *
 * 商品导航 - 商城 - 数据层
 *
 * @package   NiPHP
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class MallCategory extends Model
{
    protected $name = 'mall_category';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $type = [
        'is_show'    => 'integer',
        'is_channel' => 'integer',
        'sort'       => 'integer',
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
        'is_show',
        'is_channel',
        'sort',
        'url',
        'create_time',
        'update_time',
        'lang'
    ];
}
