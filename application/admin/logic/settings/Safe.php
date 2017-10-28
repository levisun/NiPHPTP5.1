<?php
/**
 *
 * 安全与效率设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Safe.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use app\common\logic\Config;

class Safe extends Config
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getSafeConfig()
    {
        $map = [
            ['name', 'in', 'system_portal,content_check,member_login_captcha,website_submit_captcha,upload_file_max,upload_file_type,website_static'],
            ['lang', '=', 'niphp'],
        ];

        // 实例化设置表模型
        $config = model('Config');

        $result =
        $config->field(true)
        ->where($map)
        ->select();

        $admin_data = session('admin_data');
        $data = [];
        foreach ($result as $value) {
            $value = $value->toArray();
            $data[$value['name']] = $value['value'];
        }

        $data['founder'] = $admin_data['role_id'] == 1 ? 1 : 0;

        return $data;
    }
}
