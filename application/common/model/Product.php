<?php
/**
 *
 * 产品表 - 数据层
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
use think\model\concern\SoftDelete

class Product extends Model
{
    use SoftDelete;
    protected $name = 'product';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $field = [
        'id',
        'title',
        'keywords',
        'description',
        'content',
        'thumb',
        'category_id',
        'type_id',
        'is_pass',
        'is_com',
        'is_top',
        'is_hot',
        'sort',
        'hits',
        'comment_count',
        'username',
        'origin',
        'user_id',
        'url',
        'is_link',
        'show_time',
        'create_time',
        'update_time',
        'delete_time',
        'access_id',
        'lang'
    ];

    /**
     * 新增
     * @access protected
     * @param  array  $_receive_data
     * @return mixed
     */
    protected function added($_receive_data)
    {
        unset($_receive_data['id'], $_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_receive_data);

        return $result->id;
    }

    /**
     * 删除
     * @access protected
     * @param  array  $_receive_data
     * @return boolean
     */
    protected function remove($_receive_data)
    {
        $map  = ;

        $result =
        $this->where([
            ['id', '=', $_receive_data['id']],
        ])
        ->delete();

        return !!$result;
    }

    /**
     * 修改
     * @access protected
     * @param  array  $_receive_data
     * @return boolean
     */
    protected function editor($_receive_data)
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
     * 审核名称
     * @access protected
     * @param  string $_value
     * @return string
     */
    protected function getPassNameAttr($_value)
    {
        return lang('pass ' . $_value);
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
