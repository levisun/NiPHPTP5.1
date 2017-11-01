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
    public function editor()
    {
        if (request()->isPost()) {
            $result = $this->update();
        } else {
            $basic = logic('Basic', 'settings', 'admin');
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
    private function update()
    {
        $form_data = [
            'website_name'        => input('post.website_name'),
            'website_keywords'    => input('post.website_keywords'),
            'website_description' => input('post.website_description'),
            'bottom_message'      => input('post.bottom_message', '', 'trim,htmlspecialchars'),
            'copyright'           => input('post.copyright'),
            'script'              => input('post.script', '', 'trim,htmlspecialchars'),
            '__token__'           => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Basic', 'settings', 'admin');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Basic', 'settings', 'admin');
        return $basic->update($form_data);
    }
}
