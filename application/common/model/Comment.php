<?php
/**
 *
 * 评论表 - 数据层
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

class Comment extends Model
{
    protected $name = 'comment';
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'category_id' => 'integer',
        'content_id'  => 'integer',
        'user_id'     => 'integer',
        'pid'         => 'integer',
        'is_pass'     => 'integer',
        'is_report'   => 'integer',
    ];
    protected $field = [
        'id',
        'category_id',
        'content_id' ,
        'user_id',
        'pid',
        'content',
        'is_pass',
        'is_report',
        'support',
        'report_time',
        'ip',
        'ip_attr',
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
}
