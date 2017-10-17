<?php
/**
 *
 * 栏目表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  admin\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\model;

use think\Model;

class Category extends Model
{
    protected $name = 'category';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
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
     * 获取器
     * 栏目类型
     * @access public
     * @param  int    $value
     * @return string
     */
    public function getTypeNameAttr($value, $data)
    {
        $type = [
            1 => lang('type top'),
            2 => lang('type main'),
            3 => lang('type foot'),
            4 => lang('type other')
        ];

        return $type[$data['type_id']];
    }

    /**
     * 获取器
     * 栏目模型名
     * @access public
     * @param  string $value
     * @return string
     */
    public function getModelNameAttr($value)
    {
        return lang('model ' . $value);
    }

    /**
     * 获取器
     * 栏目是否显示
     * @access public
     * @param  int    $value
     * @return string
     */
    public function getShowAttr($value, $data)
    {
        return $data['is_show'] ? lang('show') : lang('hide');
    }

    /**
     * 获取器
     * 栏目是否为频道栏目
     * @access public
     * @param  int    $value
     * @return string
     */
    public function getChannelAttr($value, $data)
    {
        return $data['is_channel'] ? lang('yes') : lang('no');
    }
}
