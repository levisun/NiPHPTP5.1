<?php
/**
 *
 * 栏目表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class Category extends Model
{
    protected $name = 'category';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
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
        'sort',
        'access_id',
        'url',
        'create_time',
        'update_time',
        'lang'
    ];
}
