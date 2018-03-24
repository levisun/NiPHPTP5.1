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
        $field = [
            'id',
            'username',
            'email',
            'last_login_ip',
            'last_login_ip_attr',
            'last_login_time',
            'create_time',
            'update_time'
        ];
        $result =
        model('common/admin')
        ->view('admin a', $field)
        ->view('role_admin ra', [], 'ra.user_id=a.id')
        ->view('role r', ['name' => 'role_name'], 'r.id=ra.role_id')
        ->order('a.update_time DESC')
        ->paginate(null, null, [
            'path' => url('user/admin'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('user/admin', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('user/admin', ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }

        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
        ];
    }

    /**
     * 查询管理员组
     * @access public
     * @param
     * @return mixed
     */
    public function role()
    {
        $map = array(
            ['status', '=', 1]
        );

        return model('common/role')
        ->where($map)
        ->order('id DESC')
        ->select();
    }

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'username'     => input('post.username'),
            'password'     => input('post.password'),
            'not_password' => input('post.not_password'),
            'email'        => input('post.email', 1),
            'role'         => input('post.role/f'),
            'salt'         => rand(111111, 999999),
            '__token__'    => input('post.__token__'),
        ];

        $result = validate('admin/admin.added', input('post.'), 'user');
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $admin_data = [
            'username' => $receive_data['username'],
            'password' => md5(md5($receive_data['password']) . $receive_data['salt']),
            'email'    => $receive_data['email'],
            'salt'     => $receive_data['salt'],
        ];
        $admin_id = model('common/admin')
        ->added($admin_data);

        $role_data = [
            'user_id' => $admin_id,
            'role_id' => $receive_data['role']
        ];
        model('common/RoleAdmin')
        ->added($role_data);

        create_action_log($receive_data['username'], 'admin_added');

        return !!$result;
    }
}
