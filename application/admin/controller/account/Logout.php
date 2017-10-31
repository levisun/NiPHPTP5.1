<?php
/**
 *
 * 注销 - 账户 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Logout.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\account;

class Logout
{

    /**
     * 注销
     * @access public
     * @param
     * @return void
     */
    public function logout()
    {
        // 实例化注销业务逻辑类
        $logout = logic('Logout', 'logic\\account', 'admin');
        // 注销用户登录
        $logout->removeAuth();
    }
}
