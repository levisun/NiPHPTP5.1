<?php
/**
 *
 * 书库文章表 - 数据层
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

class BookArticle extends Model
{
    protected $name = 'book_article';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'book_id'       => 'integer',
        'is_pass'       => 'integer',
        'sort'          => 'integer',
        'hits'          => 'integer',
        'comment_count' => 'integer',
    ];
    protected $field = [
        'id',
        'title',
        'content',
        'book_id',
        'is_pass',
        'sort',
        'hits',
        'comment_count',
        'show_time',
        'update_time',
        'delete_time',
        'create_time',
        'access_id',
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
}
