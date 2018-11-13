<?php
/**
 *
 * 权限校验 - 中间件
 *
 * @package   NiPHPCMS
 * @category  application\admin\middleware
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/11
 */

namespace app\admin\middleware;

class Auth
{

    /**
     * 权限校验
     * @access public
     * @param
     * @return void
     */
    public function handle($_request, \Closure $_next)
    {
        if ($_request->ext() !== 'do') abort(404);

        $module     = strtolower($_request->module());
        $controller = strtolower($_request->controller());
        $action     = strtolower($_request->action());

        // 用户权限校验
        if (session('?' . config('user_auth_key'))) {
            // 审核用户权限
            if (logic('common/Rbac')->checkAuth(
                    session(config('user_auth_key')),
                    $module,
                    $controller,
                    $action
            )) {
                return redirect(url('settings/info'));
            }

            // 登录页重定向
            if ($action === 'login') {
                return redirect(url('settings/info'));
            }
        } elseif ($controller !== 'account') {
            // 未登录跳转登录页
            return redirect(url('account/login') . '?back=' . urlencode($_request->url(true)));
        }

        return $_next($_request);
    }
}
