<?php
/**
 *
 * 商品表 - 商城 - 数据层
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
use think\model\concern\SoftDelete

class MallGoods extends Model
{
    use SoftDelete;
    protected $name = 'mall_goods';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'type_id'       => 'integer',
        'brand_id'      => 'integer',
        'price'         => 'integer',
        'market_price'  => 'integer',
        'number'        => 'integer',
        'is_pass'       => 'integer',
        'is_show'       => 'integer',
        'is_com'        => 'integer',
        'is_top'        => 'integer',
        'is_hot'        => 'integer',
        'sort'          => 'integer',
        'hits'          => 'integer',
        'comment_count' => 'integer',
    ];
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

    /**
     * 新增
     * @access protected
     * @param  array  $_receive_data
     * @return mixed
     */
    protected function added($_receive_data)
    {
        unset($_receive_data['id'], $_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_receive_data);

        return $result->id;
    }

    /**
     * 删除
     * @access protected
     * @param  array  $_receive_data
     * @return boolean
     */
    protected function remove($_receive_data)
    {
        $result =
        $this->where([
            ['id', '=', $_receive_data['id']],
        ])
        ->delete();

        return !!$result;
    }

    /**
     * 修改
     * @access protected
     * @param  array  $_receive_data
     * @return boolean
     */
    protected function editor($_receive_data)
    {
        unset($_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->save($_receive_data, ['id' => $_receive_data['id']]);

        return !!$result;
    }
}
