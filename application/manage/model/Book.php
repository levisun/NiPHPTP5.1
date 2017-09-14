<?php
/**
 *
 * 书库表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Book.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class Book extends Model
{
    use SoftDelete;
    protected $name = 'book';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
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
