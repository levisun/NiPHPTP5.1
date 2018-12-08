<?php
/**
 *
 * 管理员组 - 用户 - 业务层
 *
 * @package   NiPHP
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
        $result =
        model('common/role')
        ->where([
            ['id', '<>', 1],
        ])
        ->order('id DESC')
        ->append([
            'status_name'
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            if ($value->id == 1) {
                $result[$key]->url = [
                    'editor' => '',
                    'remove' => '',
                ];
            } else {
                $result[$key]->url = [
                    'editor' => url('user/role', ['operate' => 'editor', 'id' => $value->id]),
                    'remove' => url('user/role', ['operate' => 'remove', 'id' => $value->id]),
                ];
            }

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
        ->select()
        ->toArray();

        foreach ($result as $key => $value) {
            $child = $this->node($value['id']);
            if (!empty($child)) {
                $result[$key]['child'] = $child;
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
            $role_id =
            model('common/role')
            ->added([
                'name'   => input('post.name'),
                'status' => input('post.status/f'),
                'remark' => input('post.remark/f'),
            ]);

            if ($role_id == false) {
                return false;
            }

            model('common/access')
            ->where([
                ['role_id', '=', $role_id],
            ])
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
                    $node_data['node_id'] = (float) $val;
                    $node_data['level']   = (float) $key;
                    $node_data['module']  = (float) $k;

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
            $result =
            model('common/role')->field(true)
            ->where([
                ['id', '=', input('post.id/f')],
            ])
            ->find()
            ->toArray();

            create_action_log($result['name'], 'node_remove');

            model('common/role')
            ->remove([
                'id' => input('post.id/f'),
            ]);

            $role_admin =
            model('common/RoleAdmin')
            ->where([
                ['role_admin', '=', input('post.id/f')]
            ])
            ->select()
            ->toArray();

            $admin_id = [];
            foreach ($role_admin as $key => $value) {
                $admin_id[] = $value['user_id'];
            }

            model('common/admin')
            ->where([
                ['id', 'in', implode(',', $role_admin)]
            ])
            ->delete();

            model('common/role_admin')
            ->remove([
                'role_id' => input('post.id/f'),
            ]);

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
        $role_data =
        model('common/role')->field(true)
        ->where([
            ['id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();

        $result =
        model('common/access')
        ->field('node_id')
        ->where([
            ['role_id', '=', $role_data['id']],
        ])
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
            $res =
            model('common/role')
            ->editor([
                'id'     => input('post.id/f'),
                'name'   => input('post.name'),
                'status' => input('post.status/f'),
                'remark' => input('post.remark/f'),
            ]);

            if ($res === false) {
                return false;
            }

            model('common/access')
            ->where([
                ['role_id', '=', input('post.id/f')],
            ])
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
                    $node_data['node_id'] = (float) $val;
                    $node_data['level']   = (float) $key;
                    $node_data['module']  = (float) $k;

                    model('common/access')
                    ->added($node_data);
                }
            }

            create_action_log(input('post.name'), 'role_editor');

            return !!$res;
        });

        return $result;
    }
}
