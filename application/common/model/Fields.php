<?php
/**
 *
 * 自定义字段表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Fields.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class Fields extends Model
{
    protected $name = 'fields';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'category_id',
        'type_id',
        'name',
        'description',
        'is_require',
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
     * 获取器
     * 操作url
     * @access protected
     * @param
     * @return string
     */
    protected function getOperationUrlAttr($_value, $_data)
    {
        $url = [
            'editor' => url('', array('operate' => 'editor', 'id' => $_data['id'])),
            'remove' => url('', array('operate' => 'remove', 'id' => $_data['id'])),
        ];

        return $url;
    }

    /**
     * 获取器
     * 是否可为空
     * @access protected
     * @param  string $_value
     * @return string
     */
    protected function getRequireAttr($_value, $_data)
    {
        $require = [
            lang('not require'),
            lang('require'),
        ];

        return $require[$_data['is_require']];
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
}
