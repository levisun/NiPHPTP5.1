<?php
/**
 *
 * 权限校验 - 行为
 *
 * @package   NiPHPCMS
 * @category  admin\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\admin\behavior;

class Auth
{

    /**
     * 权限校验
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());

        // API不校验权限信息
        // API有自己的私有校验方法
        if ($controller === 'api') {
            return true;
        }

        $redirect = '';

        // 用户权限校验
        if (session('?' . config('user_auth_key'))) {
            // 审核用户权限
            if (logic('common/Rbac')->checkAuth(
                    session(config('user_auth_key')),
                    $module,
                    $controller,
                    $action
            )) {
                $redirect = url('settings/info');
            }

            // 登录页重定向
            if ($action === 'login') {
                $redirect = url('settings/info');
            }
        } elseif ($controller !== 'account') {
            // 未登录跳转登录页
            $redirect = url('account/login') . '?back=' . urlencode(request()->url(true));
        }

        if ($redirect) {
            echo redirect($redirect)->send();
            die();
        }

        return true;
    }
}
