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
        $receive_data = input('post.');
        $result = validate('admin/Login', $receive_data, 'account');
        if (true !== $result) {
            return $result;
        }
        return [];
    }
}
