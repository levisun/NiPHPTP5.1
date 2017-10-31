<?php
/**
 *
 * 管理员 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Admin.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\Admin as ModelAdmin;

class Admin
{

    /**
     * 保存修改栏目
     * @access public
     * @param  array  $_form_data
     * @return boolean
     */
    public function loginUpdate($_form_data)
    {
        $map  = [
            ['id', '=', $_form_data['id']],
        ];

        unset($_form_data['id']);

        $admin = new ModelAdmin;
        $result =
        $admin->allowField(true)
        ->where($map)
        ->update($_form_data);

        return !!$result;
    }
}
