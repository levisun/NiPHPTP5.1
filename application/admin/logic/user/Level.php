<?php
/**
 *
 * 会员等级 - 用户 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/5
 */
namespace app\admin\logic\user;

class Level
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
        model('common/level')
        ->order('id DESC')
        ->append([
            'status_name'
        ])
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('user/level', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('user/level', ['operate' => 'remove', 'id' => $value->id]),
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
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'id'        => input('post.id/f'),
            'name'      => input('post.name'),
            'integral'  => input('post.integral/f'),
            'status'    => input('post.status/f'),
            'remark'    => input('post.remark'),
            '__token__' => input('post.__token__'),
        ];
        $result = validate('admin/user/level.added', input('post.'));

        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['name'], 'level_added');

        return
        !!model('common/level')
        ->added($receive_data);
    }

    /**
     * 删除
     * @access public
     * @param
     *　@return mixed
     */
    public function remove()
    {
        $result =
        model('common/level')->field(true)
        ->where([
            ['id', '=', input('post.id/f')],
        ])
        ->find()
        ->toArray();

        create_action_log($result['name'], 'level_remove');

        return
        model('common/level')
        ->remove([
            'id' => input('post.id/f'),
        ]);
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return mixed
     */
    public function find()
    {
        return
        model('common/level')
        ->where([
            ['id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();
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
            'integral'  => input('post.integral/f'),
            'status'    => input('post.status/f'),
            'remark'    => input('post.remark'),
            '__token__' => input('post.__token__'),
        ];
        $result = validate('admin/user/level.editor', input('post.'));

        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['name'], 'level_editor');

        return
        model('common/level')
        ->editor($receive_data);
    }
}
