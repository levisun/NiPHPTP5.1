<?php
/**
 *
 * 注销 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  manage\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Logout.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\logic\account;

use think\facade\Session;

class Logout
{

    /**
     * 清除登录用户认证信息
     * @access public
     * @param
     * @return void
     */
    public function removeAuth()
    {
        Session::delete('ADMIN_DATA');
        Session::delete(Config::get('USER_AUTH_KEY'));
        Session::delete('_ACCESS_LIST');
    }
}
