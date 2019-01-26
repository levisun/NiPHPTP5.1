<?php
/**
 *
 * SESSION表 - 数据层
 *
 * @package   NiPHP
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019/01
 */
namespace app\common\model;

use think\Model;

class Session extends Model
{
    protected $name = 'session';
    protected $autoWriteTimestamp = false;
    protected $updateTime = 'update_time';
    protected $pk = 'session_id';
    protected $type = [
        // 'count' => 'integer',
    ];
    protected $field = [
        'session_id',
        'data',
        'update_time',
    ];

    /**
     * 新增
     * @access public
     * @param  array  $_receive_data
     * @return mixed
     */
    public function added($_receive_data)
    {
        unset($_receive_data['__token__']);

        $result =
        $this->allowField(true)
        ->create($_receive_data);

        return $result->session_id;
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
            ['session_id', '=', $_receive_data['session_id']],
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
        ->save($_receive_data, ['session_id' => $_receive_data['session_id']]);

        return !!$result;
    }
}
