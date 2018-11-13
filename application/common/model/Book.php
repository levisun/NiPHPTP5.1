<?php
/**
 *
 * 书库表 - 数据层
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
use think\model\concern\SoftDelete;

class Book extends Model
{
    use SoftDelete;
    protected $name = 'book';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'type_id' => 'integer',
        'user_id' => 'integer',
        'is_show' => 'integer',
        'sort'    => 'integer',
        'hits'    => 'integer',
    ];
    protected $field = [
        'id',
        'name',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'image',
        'type_id',
        'user_id',
        'is_show',
        'sort',
        'hits',
        'update_time',
        'delete_time',
        'create_time',
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
     * 审核名称
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getPassAttr($_value, $_data)
    {
        return lang('pass ' . $_data['is_pass']);
    }

    /**
     * 获取器
     * 审核名称
     * @access protected
     * @param  string $_value
     * @param  array  $_data
     * @return string
     */
    protected function getStatusAttr($_value, $_data)
    {
        return lang('book status ' . $_data['status']);
    }
}
