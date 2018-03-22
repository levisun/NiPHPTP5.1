<?php
/**
 *
 * 管理员 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

class Admin
{
    /**
     * 查询
     * @access public
     * @param
     * @return mixed
     */
    public function query()
    {
        $result =
        model('common/admin')
        ->view('admin a', true)
        ->view('role_admin ra', [], 'ra.user_id=a.id')
        ->view('role r', ['name' => 'role_name'], 'r.id=ra.role_id')
        ->order('sort ASC, id ASC')
        ->select();
    }
}
