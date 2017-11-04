<?php
/**
 *
 * 登录 - 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\account;

use app\common\logic\RequestLog as LogicRequestLog;
use app\admin\logic\account\Login as LogicLogin;

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
        $form_data = request()->only(
            ['username', 'password', 'captcha', '__token__'], 'post'
        );
        $login_ip = request()->ip(0, true);
        $module   = request()->module();

        // 验证请求数据
        $result = validate($form_data, 'Login', 'account', 'admin');
        if (true !== $result) {
            return $result;
        }

        // 实例化登录业务逻辑类
        $logic_login = new LogicLogin;
        $logic_request_log = new LogicRequestLog;

        // IP锁定 直接返回false
        if ($logic_request_log->isLockIp($login_ip, $module)) {
            return lang('error username or password');
        }

        // 获得用户信息
        $user_data = $logic_login->getUserData($form_data['username']);
        if (false === $user_data) {
            // 用户不存在 锁定IP
            $logic_request_log->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 登录密码错误
        if (!$logic_login->checkPassword($form_data['password'], $user_data['password'], $user_data['salt'])) {
            // 密码错误 锁定IP
            $logic_request_log->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 更新登录信息
        $logic_login->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $logic_login->createAuth($user_data);

        // 登录成功 清除锁定IP
        $logic_request_log->removeLockIp($login_ip, $module);

        return true;
    }
}
