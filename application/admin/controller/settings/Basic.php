<?php
/**
 *
 * 基础设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Basic.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Basic
{

    /**
     * 编辑基础设置
     * @access public
     * @param
     * @return array
     */
    public function editorBasicConfig()
    {
        if (request()->isPost()) {
            $result = $this->saveBasicConfig();
        } else {
            $basic = logic('Basic', 'logic\settings');
            $result = $basic->getBasicConfig();
        }

        return $result;
    }

    /**
     * 保存修改基础设置
     * @access private
     * @param
     * @return mixed
     */
    private function saveBasicConfig()
    {
        $form_data = [
            'website_name'        => request()->post('website_name'),
            'website_keywords'    => request()->post('website_keywords'),
            'website_description' => request()->post('website_description'),
            'bottom_message'      => request()->post('bottom_message', '', 'trim,htmlspecialchars'),
            'copyright'           => request()->post('copyright'),
            'script'              => request()->post('script', '', 'trim,htmlspecialchars'),
            '__token__'           => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Basic', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Basic', 'logic\settings');
        return $basic->saveBasicConfig($form_data);
    }
}
