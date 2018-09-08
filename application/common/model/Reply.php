<?php
/**
 *
 * 微信回复表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class Reply extends Model
{
    protected $name = 'reply';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'type'   => 'integer',
        'sort'   => 'integer',
        'status' => 'integer',
    ];
    protected $field = [
        'id',
        'keyword',
        'title',
        'content',
        'type',
        'image',
        'url',
        'sort',
        'status',
        'lang'
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

    /**
     * 获取器
     * 关键词回复类型
     * @access protected
     * @param  int    $value
     * @return string
     */
    protected function getTypeNameAttr($_value, $_data)
    {
        $type = [
            0 => lang('reply key'),
            1 => lang('reply auto'),
            2 => lang('reply subscribe'),
        ];

        return $type[$_data['type']];
    }

    /**
     * 获取器
     * 关键词回复类型
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
