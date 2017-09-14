<?php
/**
 *
 * 商品表 - 商城 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: MallGoods.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;
use think\model\concern\SoftDelete

class MallGoods extends Model
{
    use SoftDelete;
    protected $name = 'mall_goods';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    protected $field = [
        'id',
        'type_id',
        'brand_id',
        'name',
        'content',
        'thumb',
        'price',
        'market_price',
        'number',
        'is_pass',
        'is_show',
        'is_com',
        'is_top',
        'is_hot',
        'sort',
        'hits',
        'comment_count',
        'create_time',
        'update_time',
        'delete_time',
        'lang'
    ];
}
