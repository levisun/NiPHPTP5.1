<?php
/**
 *
 * 登录 - 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\controller\account;

use think\Request;

use app\manage\logic\account\Login as LogicAccountLogin;

class Login
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

	/**
     * 登录
     * @access public
     * @param
     * @return void
     */
    public function login($username, $password)
    {
        $login = new LogicAccountLogin;
        $login_ip = $this->request->ip(0, true);
        $module   = $this->request->module();

        if ($login->lockIp($login_ip, $module)) {
            return 40001;
        }

        // 获得用户信息
        $user_data = $login->getUser($username);
        if (false === $user_data) {
            // 用户不存在
            $login->lockIp($login_ip, $module);
            return 40001;
        }

        // 登录密码错误
        if (!$login->checkPassword($form_pwd, $user_data['password'], $user_data['salt'])) {
            $login->lockIp($login_ip, $module);
            return 40001;
        }

        // 更新登录信息
        $login->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $login->createAuth();

        // 清除锁定IP
        $login->removeLockIp($login_ip, $module);

        return 0;

    }
}
