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

class Login
{

	/**
     * 登录
     * @access public
     * @param  array  $form_data POST提交数据
     * @param  string $login_ip  登录IP
     * @param  string $module    模块
     * @return boolean
     */
    public function login($form_data, $login_ip, $module)
    {
        // 验证请求数据
        $result = validate($form_data, 'Login', 'validate\account');
        if (true !== $result) {
            return $result;
        }

        // 实例化登录业务逻辑类
        $login = logic('Login', 'logic\account');

        // IP锁定 直接返回false
        if ($login->isLockIp($login_ip, $module)) {
            return lang('error username or password');
        }

        // 获得用户信息
        $user_data = $login->getUserData($form_data['username']);
        if (false === $user_data) {
            // 用户不存在 锁定IP
            $login->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 登录密码错误
        if (!$login->checkPassword($form_data['password'], $user_data['password'], $user_data['salt'])) {
            // 密码错误 锁定IP
            $login->lockIp($login_ip, $module);
            return lang('error username or password');
        }

        // 更新登录信息
        $login->updateLogin($user_data['id'], $login_ip);

        // 生成登录用户认证信息
        $login->createAuth($user_data);

        // 登录成功 清除锁定IP
        $login->removeLockIp($login_ip, $module);

        return true;
    }
}
