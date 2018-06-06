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
        $map = [];
        if ($q = input('get.q')) {
            $map[] = ['m.usernmae', 'like', '%' . $q . '%'];
        }

        $field = [
            'id',
            'username',
            'realname',
            'nickname',
            'email',
            'phone',
            'status',
            'last_login_ip',
            'last_login_ip_attr',
            'last_login_time',
        ];

        $result =
        model('common/member')
        ->view('member m', $field)
        ->view('level_member lm', 'user_id', 'lm.user_id=m.id')
        ->view('level l', ['name'=>'level_name'], 'l.id=lm.level_id')
        ->where($map)
        ->order('m.id DESC')
        ->paginate(null, null, [
            'path' => url('user/member'),
        ]);

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
    {
        $receive_data = [
            'username'     => input('post.username'),
            'password'     => input('post.password'),
            'not_password' => input('post.not_password'),
            'email'        => input('post.email'),
            'realname'     => input('post.realname'),
            'nickname'     => input('post.nickname'),
            'portrait'     => input('post.portrait'),
            'gender'       => input('post.gender/f'),
            'birthday'     => input('post.birthday'),
            'province'     => input('post.province/f'),
            'city'         => input('post.city/f'),
            'area'         => input('post.area/f'),
            'address'      => input('post.address'),
            'phone'        => input('post.phone'),
            'level'        => input('post.level/f'),
            'status'       => input('post.status/f'),
            'salt'         => rand(111111, 999999),
            '__token__'    => input('post.__token__'),
        ];

        $result = validate('admin/user/member.added', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $member_data = [
            'username' => $receive_data['username'],
            'password' => md5_password($receive_data['password'], $receive_data['salt']),
            'email'    => $receive_data['email'],
            'realname' => $receive_data['realname'],
            'nickname' => $receive_data['nickname'],
            'portrait' => $receive_data['portrait'],
            'gender'   => $receive_data['gender'],
            'birthday' => strtotime($receive_data['birthday']),
            'province' => $receive_data['province'],
            'city'     => $receive_data['city'],
            'area'     => $receive_data['area'],
            'address'  => $receive_data['address'],
            'phone'    => $receive_data['phone'],
            'status'   => $receive_data['status'],
            'salt'     => $receive_data['salt'],
        ];

        $member_id = model('common/member')
        ->added($member_data);

        $level_data = [
            'user_id' => $member_id,
            'level_id' => $receive_data['level']
        ];
        $result = model('common/LevelMember')
        ->added($level_data);

        create_action_log($receive_data['username'], 'member_added');

        return !!$result;
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
        $field = [
            'id',
            'username',
            'email',
            'realname',
            'nickname',
            'portrait',
            'gender',
            'birthday',
            'province',
            'city',
            'area',
            'address',
            'phone',
            'status',
        ];

        $map = [
            ['m.id', '=', input('post.id/f')]
        ];

        $result =
        model('common/member')
        ->view('member m', $field)
        ->view('level_member lm', 'user_id', 'lm.user_id=m.id')
        ->where($map)
        ->find();

        return $result;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {}

}
