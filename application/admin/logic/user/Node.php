<?php
/**
 *
 * 系统节点 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\user;

class Node
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
        model('common/node')->field(true)
        ->order('sort ASC, id ASC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->status_name = $value->status_name;
            $result[$key]->level_name = $value->level_name;
            $result[$key]->url = [
                'editor' => url('user/node', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('user/node', ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }

        return node_format($result);
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
            'title'     => input('post.title'),
            'name'      => input('post.name'),
            'pid'       => input('post.pid/f', 0),
            'level'     => input('post.level/f', 1),
            'remark'    => input('post.remark'),
            'status'    => input('post.status/f', 1),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/node.added', input('post.'), 'user');
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/node')
        ->added($receive_data);

        create_action_log($receive_data['name'], 'node_added');

        return !!$result;
    }

    /**
     * 删除
     * @access public
     * @param
     *　@return mixed
     */
    public function remove()
    {
        $map  = [
            ['id', '=', input('post.id/f')],
        ];

        $result =
        model('common/node')->field(true)
        ->where($map)
        ->find();

        create_action_log($result['name'], 'node_remove');

        $receive_data = [
            'id' => input('post.id/f'),
        ];
        return model('common/node')
        ->remove($receive_data);
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

        return model('common/node')->field(true)
        ->where($map)
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
        $receive_data = [
            'id'        => input('post.id/f'),
            'title'     => input('post.title'),
            'name'      => input('post.name'),
            'pid'       => input('post.pid/f', 0),
            'level'     => input('post.level/f', 1),
            'remark'    => input('post.remark'),
            'status'    => input('post.status/f', 1),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/node.editor', input('post.'), 'user');

        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['name'], 'node_editor');

        return model('common/node')
        ->editor($receive_data);
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $receive_data = [
            'id' => input('post.sort/a'),
        ];

        create_action_log('', 'node_sort');

        return model('common/node')
        ->sort($receive_data);
    }
}
