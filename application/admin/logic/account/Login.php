<?php
/**
 *
 * 登录 - 帐户 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\account;

class Login
{
    /**
     * 登录
     * @access public
     * @param
     * @return boolean
     */
    public function login()
    {
        $receive_data =
        request()->only(
            ['username', 'password', 'captcha', '__token__'], 'post'
        );
        $result = validate('admin/account/login', $receive_data);
        if (true !== $result) {
            return $result;
        }

        // IP锁定 直接返回false
        $login_ip = request()->ip();
        $module   = request()->module();
        if (logic('common/RequestLog')->isLockIp($login_ip, $module)) {
            return lang('error username or password');
        }

        // 获得用户信息
        $user_data = $this->getUserData($receive_data['username']);
        if (false === $user_data) {
            // 用户不存在 锁定IP
            logic('common/RequestLog')->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 登录密码错误
        if ($user_data['password'] !== md5_password($receive_data['password'], $user_data['salt'])) {
            // 密码错误 锁定IP
            logic('common/RequestLog')->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 更新登录信息
        $this->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $this->createAuth($user_data);

        // 登录成功 清除锁定IP
        logic('common/RequestLog')->removeLockIp($login_ip, $module);

        create_action_log('', 'admin_login');

        return true;
    }

    /**
     * 获得用户信息
     * @access private
     * @param  string $_username 用户名
     * @return array OR false
     */
    private function getUserData($_username)
    {
        $result =
        model('common/Admin')->view('admin a', 'id,username,password,email,salt')
        // 管理员组关系表
        ->view('role_admin ra', 'user_id', 'a.id=ra.user_id')
        // 组表
        ->view('role r', ['id'=>'role_id', 'name'=>'role_name'], 'r.id=ra.role_id')
        ->where([
            ['a.username', '=', $_username],
        ])
        ->find();

        return !empty($result) ? $result->toArray() : false;
    }

    /**
     * 更新登录信息
     * @access private
     * @param  int    $_user_id  管理员ID
     * @param  string $_login_ip 登录IP
     * @return boolean
     */
    private function updateLogin($_user_id, $_login_ip)
    {
        $ip_attr = '';
        $result = logic('common/IpInfo')->getInfo();
        if (!is_null($result)) {
            $ip_attr = $result['region'] . $result['city'];
        }

        $update_data = [
            // 登录IP
            'last_login_ip'      => $_login_ip,
            // 登录IP所在地区
            'last_login_ip_attr' => $ip_attr,
            'id'                 => $_user_id,
        ];

        return
        model('common/Admin')
        ->update($update_data);
    }

    /**
     * 生成登录用户认证信息
     * @access private
     * @param  array  $_user_data 管理员数据
     * @return void
     */
    private function createAuth($_user_data)
    {
        // 删除危险信息
        unset($_user_data['password'], $_user_data['salt'], $_user_data['user_id']);
        // 生成管理员数据
        session('admin_data', $_user_data);
        // 生成认证ID
        session(config('user_auth_key'), $_user_data['id']);
        // 生成认证权限
        logic('common/Rbac')->checkAuth(session(config('user_auth_key')));
    }
}
