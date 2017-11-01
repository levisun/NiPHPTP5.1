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
    public function editor()
    {
        if (request()->isPost()) {
            $result = $this->update();
        } else {
            $basic = logic('Email', 'settings', 'admin');
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
    private function update()
    {
        $params = [
            'smtp_host'       => input('post.smtp_host'),
            'smtp_port'       => input('post.smtp_port/f'),
            'smtp_username'   => input('post.smtp_username'),
            'smtp_password'   => input('post.smtp_password'),
            'smtp_from_email' => input('post.smtp_from_email'),
            'smtp_from_name'  => input('post.smtp_from_name'),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Email', 'settings', 'admin');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Email', 'settings', 'admin');
        return $basic->update($form_data);
    }
}
