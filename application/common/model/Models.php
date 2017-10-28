<?php
/**
 *
 * 栏目表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Models.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
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
     * 获取器
     * 操作url
     * @access public
     * @param
     * @return string
     */
    public function getOperationUrlAttr($_value, $_data)
    {
        $url = [
            'editor' => url('', array('operate' => 'editor', 'id' => $_data['id'])),
            'remove' => url('', array('operate' => 'remove', 'id' => $_data['id'])),
        ];

        return $url;
    }

    /**
     * 获取器
     * 模型名称
     * @access public
     * @param  int    $_value
     * @return string
     */
    public function getModelNameAttr($_value, $_data)
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
     * @access public
     * @param  int    $_value
     * @return string
     */
    public function getModelStatusAttr($_value, $_data)
    {
        $status = [
            0 => lang('status no'),
            1 => lang('status yes'),
        ];

        return $status[$_data['status']];
    }
}
