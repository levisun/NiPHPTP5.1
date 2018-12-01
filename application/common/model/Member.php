<?php
/**
 *
 * 会员表 - 数据层
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

class Member extends Model
{
    protected $name = 'member';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $type = [
        'province_id' => 'integer',
        'city_id'     => 'integer',
        'area_id'     => 'integer',
        'status'      => 'integer',
        'salt'        => 'integer',
    ];
    protected $field = [
        'id',
        'username',
        'password',
        'email',
        'realname',
        'nickname',
        'portrait',
        'gender',
        'birthday',
        'province_id',
        'city_id',
        'area_id',
        'address',
        'phone',
        'status',
        'salt',
        'last_login_ip',
        'last_login_ip_attr',
        'last_login_time',
        'create_time',
        'update_time',
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
        ->save($_receive_data, ['id' => $_receive_data['id']]);

        return !!$result;
    }

    /**
     * 获取器
     * 节点状态
     * @access protected
     * @param  int    $value
     * @return string
     */
    protected function getStatusNameAttr($_value, $_data)
    {
        $status = [
            0 => lang('status no'),
            1 => lang('status yes'),
        ];

        return $status[$_data['status']];
    }
}
