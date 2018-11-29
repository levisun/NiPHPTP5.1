<?php
/**
 *
 * IP地域信息表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class IpInfo extends Model
{
    protected $name = 'ipinfo';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $dateFormat = false;
    protected $pk = 'id';
    protected $type = [
        'country_id'  => 'integer',
        'province_id' => 'integer',
        'city_id'     => 'integer',
        'area_id'     => 'integer'
    ];
    protected $field = [
        'id',
        'ip',
        'country_id',
        'province_id',
        'city_id',
        'area_id',
        'isp',
        'update_time',
        'create_time'
    ];

    /**
     * 新增
     * @access public
     * @param  array  $_receive_data
     * @return mixed
     */
    public function added($_receive_data)
    {
        unset($_receive_data['id'], $_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_receive_data);

        return $result->id;
    }

    /**
     * 删除
     * @access public
     * @param  array  $_receive_data
     * @return boolean
     */
    public function remove($_receive_data)
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
     * @access public
     * @param  array  $_receive_data
     * @return boolean
     */
    public function editor($_receive_data)
    {
        unset($_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->where([
            ['id', '=', $_receive_data['id']],
        ])
        ->update($_receive_data);

        return !!$result;
    }
}
