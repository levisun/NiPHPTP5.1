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
        $result =
        model('common/role')
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
            'remark'    => input('post.remark/f'),
            'node'      => input('post.node/a'),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/role.added', input('post.'), 'user');
        if (true !== $result) {
            return $result;
        }


        halt($receive_data);
    }
}
