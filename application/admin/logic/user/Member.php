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
        ->append([
            'status_name'
        ])
        ->paginate(null, null, [
            'path' => url('user/member'),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->url = [
                'editor' => url('user/member', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('user/member', ['operate' => 'remove', 'id' => $value->id]),
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
     * 获得地址
     * @access public
     * @param  intval $parent_id 父级地区ID
     * @return array
     */
    public function region()
    {
        $result =
        model('common/region')
        ->field(['id', 'pid', 'name'])
        ->where([
            ['pid', '=', input('post.region_id/f', 100000)],
        ])
        ->order('id ASC')
        ->cache(true)
        ->select()
        ->toArray();

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
        $result =
        model('common/level')
        ->field(true)
        ->where([
            ['status', '=', 1]
        ])
        ->order('id DESC')
        ->select()
        ->toArray();

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
        $result = model('common/member')->transaction(function(){
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

            $member_id =
            model('common/member')
            ->added([
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
            ]);

            model('common/LevelMember')
            ->added([
                'user_id'  => $member_id,
                'level_id' => $receive_data['level']
            ]);

            create_action_log($receive_data['username'], 'member_added');

            return !!$member_id;
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
        $result = model('common/member')->transaction(function(){
            $result =
            model('common/member')
            ->field(true)
            ->where([
                ['id', '=', input('post.id/f')],
            ])
            ->find()
            ->toArray();

            create_action_log($result['username'], 'member_remove');

            $result =
            model('common/member')
            ->remove([
                'id' => input('post.id/f'),
            ]);

            if ($result) {
                model('common/LevelMember')
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
        $result =
        model('common/member')
        ->view('member m', [
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
            'status'
        ])
        ->view('level_member lm', 'level_id', 'lm.user_id=m.id')
        ->where([
            ['m.id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();

        return $result;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $result = model('common/member')->transaction(function(){
            $receive_data = [
                'id'           => input('post.id/f'),
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

            if ($receive_data['password']) {
                $result = validate('admin/user/member.editor', input('post.'));
                $member_data = [
                    'id'       => $receive_data['id'],
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
            } else {
                $result = validate('admin/user/member.editorNoPwd', input('post.'));
                $member_data = [
                    'id'       => $receive_data['id'],
                    'username' => $receive_data['username'],
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
                ];
            }

            if (true !== $result) {
                return $result;
            }

            $result =
            model('common/member')
            ->editor($member_data);

            model('common/LevelMember')
            ->editor([
                'user_id'  => $receive_data['id'],
                'level_id' => $receive_data['level'],
            ]);

            create_action_log($receive_data['username'], 'member_editor');

            return true;
        });

        return $result;
    }
}
