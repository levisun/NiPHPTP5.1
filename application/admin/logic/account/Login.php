<?php
/**
 *
 * 登录 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
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
        $receive_data = request()->only(
            ['username', 'password', 'captcha', '__token__'], 'post'
        );
        $result = validate('admin/Login', $receive_data, 'account');
        if (true !== $result) {
            return backData($result, 'ERROR');
        }

        // IP锁定 直接返回false
        $login_ip = request()->ip(0, true);
        $module   = request()->module();
        if (logic('common/RequestLog')->isLockIp($login_ip, $module)) {
            return backData(lang('error username or password'), 'ERROR');
        }

        // 获得用户信息
        $user_data = $this->getUserData($receive_data['username']);
        if (false === $user_data) {
            // 用户不存在 锁定IP
            logic('common/RequestLog')->lockIp($login_ip, $module);
            return backData(lang('error username or password'), 'ERROR');
        }

        // 登录密码错误
        if (!$this->checkPassword($receive_data['password'], $user_data['password'], $user_data['salt'])) {
            // 密码错误 锁定IP
            logic('common/RequestLog')->lockIp($login_ip, $module);
            return backData(lang('error username or password'), 'ERROR');
        }

        // 更新登录信息
        $this->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $this->createAuth($user_data);

        // 登录成功 清除锁定IP
        logic('common/RequestLog')->removeLockIp($login_ip, $module);

        return backData(lang('login success'), 'SUCCESS');
    }

    /**
     * 获得用户信息
     * @access private
     * @param  string $_username 用户名
     * @return array OR false
     */
    private function getUserData($_username)
    {
        $map = [
            ['a.username', '=', $_username],
        ];
        $result =
        model('common/Admin')->view('admin a', 'id,username,password,email,salt')
        // 管理员组关系表
        ->view('role_admin ra', 'user_id', 'a.id=ra.user_id')
        // 组表
        ->view('role r', ['id'=>'role_id', 'name'=>'role_name'], 'r.id=ra.role_id')
        ->where($map)
        ->find();

        return !empty($result) ? $result : false;
    }

    /**
     * 验证登录密码
     * @access private
     * @param  string  $_form_pws 请求密码
     * @param  string  $_password 密码
     * @param  string  $_salt     佐料
     * @return boolean
     */
    private function checkPassword($_rec_psw, $_password, $_salt)
    {
        $_rec_psw = md5(trim($_rec_psw));
        $_rec_psw = md5($_rec_psw . $_salt);
        return $_password === $_rec_psw;
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
        $request_url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $_login_ip;
        $result = file_get_contents($request_url);
        if ($result) {
            $ip = json_decode($result, true);
            $ip_attr  = $ip['data']['region'] . $ip['data']['city'];
            $ip_attr .= '[' . $ip['data']['isp'] . ']';
        }

        $update_data = [
            // 登录IP
            'last_login_ip'      => $_login_ip,
            // 登录IP所在地区
            'last_login_ip_attr' => $ip_attr,
            'id'                 => $_user_id,
        ];

        return model('common/Admin')->update($update_data);
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
    }
}
