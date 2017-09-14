<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Account.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\controller;

use app\manage\controller\Base;

class Account extends Base
{

    public function login()
    {
        $login = new LogicAccountLogin;
        $username = $this->request->post('username');
        $form_pwd = $this->request->post('password');
        $login_ip = $this->request->ip(0, true);
        $module   = $this->request->module();

        if ($login->lockIp($login_ip, $module)) {
            return [];
        }

        // 获得用户信息
        $user_data = $login->getUser($username);
        if (false === $user_data) {
            // 用户不存在
            $login->lockIp($login_ip, $module);
            return [];
        }

        // 登录密码错误
        if (!$login->checkPassword($form_pwd, $user_data['password'], $user_data['salt'])) {
            $login->lockIp($login_ip, $module);
            return [];
        }

        // 更新登录信息
        $login->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $login->createAuth();

        // 清除锁定IP
        $login->removeLockIp($login_ip, $module);

    }

    public function logout()
    {
        # code...
    }
}
