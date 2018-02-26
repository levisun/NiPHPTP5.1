<?php
/**
 *
 * 栏目表 - 数据层
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

class Models extends Model
{
    protected $name = 'model';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'name',
        'table_name',
        'remark',
        'status',
        'sort',
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
        $map  = [
            ['id', '=', $_receive_data['id']],
        ];

        $result =
        $this->where($map)
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
        $map  = [
            ['id', '=', $_receive_data['id']],
        ];

        unset($_receive_data['id'], $_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->where($map)
        ->update($_receive_data);

        return !!$result;
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $form_data = [
            'id' => input('post.sort/a'),
        ];

        foreach ($form_data['id'] as $key => $value) {
            $data[] = [
                'id'   => $key,
                'sort' => $value,
            ];
        }

        $result =
        $this->saveAll($data);

        return !!$result;
    }

    /**
     * 获取器
     * 模型名称
     * @access protected
     * @param  int    $_value
     * @return string
     */
    protected function getModelNameAttr($_value, $_data)
    {
        $name = [
            'article'  => lang('model article'),
            'picture'  => lang('model picture'),
            'download' => lang('model download'),
            'page'     => lang('model page'),
            'feedback' => lang('model feedback'),
            'message'  => lang('model message'),
            'product'  => lang('model product'),
            'link'     => lang('model link'),
            'external' => lang('model external'),
        ];

        return isset($name[$_data['name']]) ? $name[$_data['name']] : $_data['name'];
    }

    /**
     * 获取器
     * 模型状态
     * @access protected
     * @param  int    $_value
     * @return string
     */
    protected function getModelStatusAttr($_value, $_data)
    {
        $status = [
            0 => lang('status no'),
            1 => lang('status yes'),
        ];

        return $status[$_data['status']];
    }
}
