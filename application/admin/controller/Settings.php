<?php
/**
 *
 * 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Settings.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Settings extends Base
{

    /**
     * 系统信息
     * @access public
     * @param
     * @return mixed
     */
    public function info()
    {
        $result = action('Info/info', [], 'controller\settings');
        $this->assign('json_data_info', json_encode($result));
        return $this->fetch();
    }

    /**
     * 基础设置
     * @access public
     * @param
     * @return mixed
     */
    public function basic()
    {
        if ($this->request->isPost()) {
            $params = [
                'form_data' => [
                    'website_name'        => $this->request->post('website_name'),
                    'website_keywords'    => $this->request->post('website_keywords'),
                    'website_description' => $this->request->post('website_description'),
                    'bottom_message'      => $this->request->post('bottom_message', '', 'trim,htmlspecialchars'),
                    'copyright'           => $this->request->post('copyright'),
                    'script'              => $this->request->post('script', '', 'trim,htmlspecialchars'),
                    '__token__'           => $this->request->post('__token__'),
                ],
            ];

            $result = action('Basic/saveBasicConfig', $params, 'controller\settings');

            $this->showMessage($result);
        }

        $result = action('Basic/getBasicConfig', [], 'controller\settings');
        $this->assign('json_data_basic', json_encode($result));
        return $this->fetch();
    }

    public function lang()
    {
        # code...
    }

    public function image()
    {
        $result = action('Image/getImageConfig', [], 'controller\settings');
        $this->assign('json_data_image', json_encode($result));
        return $this->fetch();
    }
}
