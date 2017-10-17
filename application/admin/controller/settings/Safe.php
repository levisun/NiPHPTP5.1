<?php
/**
 *
 * 安全与效率设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Safe.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Safe
{

    /**
     * 编辑安全与效率设置
     * @access public
     * @param
     * @return array
     */
    public function editorSafeConfig()
    {
        if (request()->isPost()) {
            $result = $this->saveSafeConfig();
        } else {
            $basic = logic('Safe', 'logic\settings');
            $result = $basic->getSafeConfig();
        }

        return $result;
    }

    /**
     * 保存修改安全与效率设置
     * @access private
     * @param
     * @return mixed
     */
    private function saveSafeConfig()
    {
        $form_data = [
            'content_check'          => request()->post('content_check/f'),
            'member_login_captcha'   => request()->post('member_login_captcha/f'),
            'website_submit_captcha' => request()->post('website_submit_captcha/f'),
            'website_static'         => request()->post('website_static/f'),
            'upload_file_max'        => request()->post('upload_file_max/f'),
            'upload_file_type'       => request()->post('upload_file_type'),
            '__token__'              => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Safe', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Safe', 'logic\settings');
        return $basic->saveSafeConfig($form_data);
    }
}
