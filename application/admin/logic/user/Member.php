<?php
/**
 *
 * 会员 - 用户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\user
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/5
 */
namespace app\admin\logic\user;

class Member
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
        model('common/member')
        ->view('member m', 'id,username,realname,nickname,email,phone,status')
        ->view('level_member lm', 'user_id', 'lm.user_id=m.id')
        ->view('level l', ['name'=>'level_name'], 'l.id=lm.level_id')
        ->order('m.id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->status_name = $value->status_name;
            $result[$key]->url = [
                'editor' => url('user/member', ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url('user/member', ['operate' => 'remove', 'id' => $value['id']]),
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
     * 获得地址
     * @access public
     * @param  intval $parent_id 父级地区ID
     * @return array
     */
    public function region()
    {
        $map  = [
            ['pid', '=', input('post.region_id/f', 1)],
        ];

        $result =
        model('common/region')->field(['id', 'pid', 'name'])
        ->where($map)
        ->select();

        return $result;
    }

    /**
     * 获得会员组数据
     * @access public
     * @param
     * @return array
     */
    public function level()
    {
        $map = [
            ['status', '=', 1]
        ];

        $result =
        model('common/level')->field(true)
        ->where($map)
        ->order('id DESC')
        ->select();

        return $result;
    }

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {}

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
    {}

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {}

}
