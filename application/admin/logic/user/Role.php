<?php
/**
 *
 * 管理员组 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

class Role
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
            ['id', '<>', 1],
        ];
        $result =
        model('common/role')
        ->where($map)
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url('user/role'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->status_name = $value->status_name;
            if ($value->id == 1) {
                $result[$key]->url = [
                    'editor' => '',
                    'remove' => '',
                ];
            } else {
                $result[$key]->url = [
                    'editor' => url('user/role', ['operate' => 'editor', 'id' => $value['id']]),
                    'remove' => url('user/role', ['operate' => 'remove', 'id' => $value['id']]),
                ];
            }

        }

        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
        ];
    }

    /**
     * 获得权限节点
     * @access public
     * @param  int    $_parent_id 父ID
     * @return array
     */
    public function node($_parent_id = 0)
    {
        $map = [
            ['status', '=', 1]
        ];

        if ($_parent_id) {
            $map[] = ['pid', '=', $_parent_id];
        } else {
            $map[] = ['id', '=', 1];
        }

        $result =
        model('common/node')
        ->where($map)
        ->order('sort ASC')
        ->select();

        foreach ($result as $key => $value) {
            $child = $this->node($value->id);
            if (!empty($child)) {
                $result[$key]->child = $child;
            }

        }
        return $result;
    }

    /**
     * 增加
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'name'      => input('post.name'),
            'status'    => input('post.status/f'),
            'remark'    => input('post.remark'),
            'node'      => input('post.node/a'),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/user/role.added', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/role')->transaction(function(){
            $role_data = [
                'name'   => input('post.name'),
                'status' => input('post.status/f'),
                'remark' => input('post.remark/f'),
            ];
            $role_id = model('common/role')
            ->added($role_data);

            if ($role_id == false) {
                return false;
            }


            $map = [
                ['role_id', '=', $role_id],
            ];
            model('common/access')
            ->where($map)
            ->delete();

            $node = input('post.node/a');
            $node_data = [
                'role_id' => $role_id,
                'status'  => 1,
            ];
            foreach ($node as $key => $value) {
                foreach ($value as $k => $val) {
                    $k = explode('_', $k);
                    $k = !empty($k[1]) ? $k[1] : $k[0];
                    $node_data['node_id'] = $val;
                    $node_data['level']   = $key;
                    $node_data['module']  = $k;

                    model('common/access')
                    ->added($node_data);
                }
            }

            create_action_log($role_data['name'], 'role_added');

            return !!$role_id;

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
        $result = model('common/role')->transaction(function(){
            $map  = [
                ['id', '=', input('post.id/f')],
            ];

            $result =
            model('common/role')->field(true)
            ->where($map)
            ->find();

            create_action_log($result['name'], 'node_remove');

            $receive_data = [
                'id' => input('post.id/f'),
            ];
            model('common/role')
            ->remove($receive_data);

            $map = [
                ['role_admin', '=', input('post.id/f')]
            ];
            $role_admin =
            model('common/RoleAdmin')
            ->where($map)
            ->select();
            $admin_id = [];
            foreach ($role_admin as $key => $value) {
                $admin_id[] = $value['user_id'];
            }

            $map = [
                ['id', 'in', implode(',', $role_admin)]
            ];
            model('common/admin')
            ->where($map)
            ->delete();

            $receive_data = [
                'role_id' => input('post.id/f'),
            ];
            model('common/role_admin')
            ->remove($receive_data);
        });

    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return mixed
     */
    public function find()
    {
        $map = [
            ['id', '=', input('post.id/f')]
        ];

        $role_data = model('common/role')->field(true)
        ->where($map)
        ->find();

        $map = [
            ['role_id', '=', $role_data['id']],
        ];
        $result = model('common/access')
        ->field('node_id')
        ->where($map)
        ->select();
        $access = [];
        foreach ($result as $key => $value) {
            $access[] = $value['node_id'];
        }

        $role_data['access'] = $access;

        return $role_data;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'id'        => input('post.id/f'),
            'name'      => input('post.name'),
            'status'    => input('post.status/f'),
            'remark'    => input('post.remark'),
            'node'      => input('post.node/a'),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/user/role.editor', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/role')->transaction(function(){
            $role_data = [
                'id'     => input('post.id/f'),
                'name'   => input('post.name'),
                'status' => input('post.status/f'),
                'remark' => input('post.remark/f'),
            ];
            $res = model('common/role')
            ->editor($role_data);

            if ($res === false) {
                return false;
            }


            $map = [
                ['role_id', '=', input('post.id/f')],
            ];
            model('common/access')
            ->where($map)
            ->delete();

            $node = input('post.node/a');
            $node_data = [
                'role_id' => input('post.id/f'),
                'status'  => 1,
            ];
            foreach ($node as $key => $value) {
                foreach ($value as $k => $val) {
                    $k = explode('_', $k);
                    $k = !empty($k[1]) ? $k[1] : $k[0];
                    $node_data['node_id'] = $val;
                    $node_data['level']   = $key;
                    $node_data['module']  = $k;

                    model('common/access')
                    ->added($node_data);
                }
            }

            create_action_log($role_data['name'], 'role_editor');

            return !!$res;
        });

        return $result;
    }
}
