<?php
/**
 *
 * 栏目表 - 数据层
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

class Category extends Model
{
    protected $name = 'category';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $type = [
        'type_id'    => 'integer',
        'model_id'   => 'integer',
        'is_show'    => 'integer',
        'is_channel' => 'integer',
        'sort'       => 'integer',
        'access_id'  => 'integer',
    ];
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
    public function remove($_id)
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
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort($_receive_data)
    {
        $data = [];
        foreach ($_receive_data['id'] as $key => $value) {
            $data[] = [
                'id'   => (float) $key,
                'sort' => (float) $value,
            ];
        }

        $result =
        $this->saveAll($data);

        return !!$result;
    }

    /**
     * 获取器
     * 栏目类型
     * @access protected
     * @param  int    $value
     * @return string
     */
    protected function getTypeNameAttr($_value, $_data)
    {
        $type = [
            1 => lang('type top'),
            2 => lang('type main'),
            3 => lang('type foot'),
            4 => lang('type other')
        ];

        return $type[$_data['type_id']];
    }

    /**
     * 获取器
     * 栏目模型名
     * @access protected
     * @param  string $_value
     * @return string
     */
    protected function getModelNameAttr($_value)
    {
        return lang('model ' . $_value);
    }

    /**
     * 获取器
     * 栏目是否显示
     * @access protected
     * @param  int    $_value
     * @return string
     */
    protected function getShowAttr($_value, $_data)
    {
        return $_data['is_show'] ? lang('show') : lang('hide');
    }

    /**
     * 获取器
     * 栏目是否为频道栏目
     * @access protected
     * @param  int    $_value
     * @return string
     */
    protected function getChannelAttr($_value, $_data)
    {
        return $_data['is_channel'] ? lang('yes') : lang('no');
    }
}
