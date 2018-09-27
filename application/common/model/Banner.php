<?php
/**
 *
 * 幻灯片表 - 数据层
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

class Banner extends Model
{
    protected $name = 'banner';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $pk = 'id';
    protected $type = [
        'pid'    => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
        'hits'   => 'integer',
        'sort'   => 'integer'
    ];
    protected $field = [
        'id',
        'pid',
        'name',
        'title',
        'width',
        'height',
        'image',
        'url',
        'hits',
        'sort',
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
}
