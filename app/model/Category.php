<?php
/**
 *
 * 数据层
 * 栏目表
 *
 * @package   NiPHP
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

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
        'sort_order' => 'integer',
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
        'sort_order',
        'access_id',
        'url',
        'create_time',
        'update_time',
        'lang'
    ];

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
