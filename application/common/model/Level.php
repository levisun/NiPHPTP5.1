<?php
/**
 *
 * 会员等级表 - 数据层
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

class Level extends Model
{
    protected $name = 'level';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'integral' => 'integer',
        'status'   => 'integer',
    ];
    protected $field = [
        'id',
        'name',
        'integral',
        'status',
        'remark'
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
     * 等级状态
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
