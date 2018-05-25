<?php
/**
 *
 * 会员等级 - 用户 - 业务层
 *
 * @package   NiPHPCMS
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
        ->paginate(null, null, [
            'path' => url('user/level'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->status_name = $value->status_name;
            $result[$key]->url = [
                'editor' => url('user/level', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('user/level', ['operate' => 'remove', 'id' => $value['id']]),
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
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {

    }

    /**
     * 删除
     * @access public
     * @param
     *　@return mixed
     */
    public function remove()
    {}

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

        return
        model('common/level')
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

        return model('common/level')
        ->editor($receive_data);
    }
}
