<?php
/**
 *
 * 管理员 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

use app\admin\logic\Upload;

class Admin extends Upload
{

    /**
     * 查询
     * @access public
     * @param
     * @return mixed
     */
    public function query()
    {
        $map = [
            ['a.id', '<>', 1]
        ];
        if ($q = input('get.q')) {
            $map[] = ['a.usernmae', 'like', '%' . $q . '%'];
        }

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
        ->where($map)
        ->order('a.update_time DESC, a.id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('user/admin', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('user/admin', ['operate' => 'remove', 'id' => $value->id]),
            ];
        }

        $list = $result->toArray();

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
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
        return
        model('common/role')
        ->where([
            ['status', '=', 1],
            ['id', '<>', 1],
        ])
        ->order('id DESC')
        ->select()
        ->toArray();
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
            'email'        => input('post.email'),
            'role'         => input('post.role/f'),
            'salt'         => rand(111111, 999999),
            '__token__'    => input('post.__token__'),
        ];

        $result = validate('admin/user/admin.added', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/admin')->transaction(function(){
            $admin_id =
            model('common/admin')
            ->added([
                'username' => $receive_data['username'],
                'password' => md5_password($receive_data['password'], $receive_data['salt']),
                'email'    => $receive_data['email'],
                'salt'     => $receive_data['salt'],
            ]);

            $result =
            model('common/RoleAdmin')
            ->added([
                'user_id' => $admin_id,
                'role_id' => $receive_data['role']
            ]);

            create_action_log($receive_data['username'], 'admin_added');

            return true;
        });

        return $result;
    }

    /**
     * 删除
     * @access public
     * @param
     *　@return mixed
     */
    public function remove()
    {
        $result = model('common/admin')->transaction(function(){
            $result =
            model('common/admin')
            ->field(true)
            ->where([
                ['id', '=', input('post.id/f')],
            ])
            ->find();

            create_action_log($result['username'], 'admin_remove');

            $result =
            model('common/admin')
            ->remove([
                'id' => input('post.id/f'),
            ]);

            if ($result) {
                model('common/RoleAdmin')
                ->remove([
                    'user_id' => input('post.id/f'),
                ]);
            }

            return true;
        });

        return $result;
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return mixed
     */
    public function find()
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

        return
        model('common/admin')
        ->view('admin a', $field)
        ->view('role_admin ra', ['role_id'], 'ra.user_id=a.id')
        ->where([
            ['a.id', '=', input('post.id/f')]
        ])
        ->find();
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $result = model('common/admin')->transaction(function(){
            $receive_data = [
                'id'           => input('post.id/f'),
                'username'     => input('post.username'),
                'password'     => input('post.password'),
                'not_password' => input('post.not_password'),
                'email'        => input('post.email'),
                'role'         => input('post.role/f'),
                'salt'         => rand(111111, 999999),
                '__token__'    => input('post.__token__'),
            ];

            if ($receive_data['password']) {
                $result = validate('admin/user/admin.editor', input('post.'));
                $admin_data = [
                    'id'       => $receive_data['id'],
                    'username' => $receive_data['username'],
                    'password' => md5_password($receive_data['password'], $receive_data['salt']),
                    'email'    => $receive_data['email'],
                    'salt'     => $receive_data['salt'],
                ];
            } else {
                $result = validate('admin/user/admin.editorNoPwd', input('post.'));
                $admin_data = [
                    'id'       => $receive_data['id'],
                    'username' => $receive_data['username'],
                    'email'    => $receive_data['email'],
                ];
            }

            if (true !== $result) {
                return $result;
            }

            $result =
            model('common/admin')
            ->editor($admin_data);

            model('common/RoleAdmin')
            ->editor([
                'user_id' => $receive_data['id'],
                'role_id' => $receive_data['role'],
            ]);

            create_action_log($receive_data['username'], 'admin_editor');

            return true;
        });

        return $result;
    }
}
