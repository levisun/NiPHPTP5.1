<?php
/**
 *
 * 管理员组关系表 - 数据层
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

class RoleAdmin extends Model
{
    protected $name = 'role_admin';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
    protected $field = [
        'id',
        'user_id',
        'role_id',
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
            ['user_id', '=', $_receive_data['user_id']],
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
            ['user_id', '=', $_receive_data['user_id']],
        ])
        ->update($_receive_data);

        return !!$result;
    }
}
