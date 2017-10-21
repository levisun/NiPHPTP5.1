<?php
/**
 *
 * 邮箱设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Email.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

class Email
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getEmailConfig()
    {
        $map = [
            ['name', 'in', 'smtp_host,smtp_port,smtp_username,smtp_password,smtp_from_email,smtp_from_name'],
            ['lang', '=', 'niphp'],
        ];

        // 实例化设置表模型
        $config = model('Config');

        $result =
        $config->field(true)
        ->where($map)
        ->select();

        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = $value['value'];
        }

        return $data;
    }

    /**
     * 保存修改基础设置
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function update($_form_data)
    {
        // 实例化设置表模型
        $config = model('Config');

        $map = $data = [];
        foreach ($_form_data as $key => $value) {
            $map  = [
                ['name', '=', $key],
            ];
            $data = ['value' => $value];

            $config->where($map)
            ->update($data);
        }

        return true;
    }
}
