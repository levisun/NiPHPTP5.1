<?php
/**
 *
 * 产品表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Product.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;
use think\model\concern\SoftDelete

class Product extends Model
{
    use SoftDelete;
    protected $name = 'product';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    protected $field = [
        'id',
        'title',
        'keywords',
        'description',
        'content',
        'thumb',
        'category_id',
        'type_id',
        'is_pass',
        'is_com',
        'is_top',
        'is_hot',
        'sort',
        'hits',
        'comment_count',
        'username',
        'origin',
        'user_id',
        'url',
        'is_link',
        'show_time',
        'create_time',
        'update_time',
        'delete_time',
        'access_id',
        'lang'
    ];
}
