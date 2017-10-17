<?php
/**
 *
 * 邮箱设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Email.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Email
{

    /**
     * 编辑邮箱设置
     * @access public
     * @param
     * @return array
     */
    public function editorEmailConfig()
    {
        if (request()->isPost()) {
            $result = $this->saveEmailConfig();
        } else {
            $basic = logic('Email', 'logic\settings');
            $result $basic->getEmailConfig();
        }

        return $result;
    }

    /**
     * 保存修改邮箱设置
     * @access private
     * @param
     * @return mixed
     */
    private function saveEmailConfig()
    {
        $params = [
            'smtp_host'       => request()->post('smtp_host'),
            'smtp_port'       => request()->post('smtp_port/f'),
            'smtp_username'   => request()->post('smtp_username'),
            'smtp_password'   => request()->post('smtp_password'),
            'smtp_from_email' => request()->post('smtp_from_email'),
            'smtp_from_name'  => request()->post('smtp_from_name'),
            '__token__'       => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Email', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Email', 'logic\settings');
        return $basic->saveEmailConfig($form_data);
    }
}
