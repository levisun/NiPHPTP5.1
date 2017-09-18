<?php
/**
 *
 * 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  manage\controller
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

    /**
     * 登录
     * @access public
     * @param
     * @return void
     */
    public function login()
    {
        if ($this->request->isPost()) {
            // 验证请求数据
            $validata = $this->request->only(['username', 'password', 'captcha'], 'post');
            $result = $this->validate($validata, 'Admin.login');
            if (true !== $result) {
                $this->error($result);
            }

            $params = [
                'form_data' => $this->request->only(['username', 'password'], 'post'),
                'login_ip'  => $this->request->ip(0, true),
                'module'    => $this->request->module(),
            ];
            $result = action('Login/login', $params, 'controller\account');
            if (true === $result) {
                # code...
            } else {

            }
        }

        return $this->fetch();
    }

    public function logout()
    {
        action('Login/logout', [], 'controller\account');
    }
}
