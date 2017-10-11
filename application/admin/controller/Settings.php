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

            $this->showMessage($result, lang('save success'));
        }

        $result = action('Basic/getBasicConfig', [], 'controller\settings');
        $this->assign('json_data_basic', json_encode($result));
        return $this->fetch();
    }

    /**
     * 语言设置
     * @access public
     * @param
     * @return mixed
     */
    public function lang()
    {
        if ($this->request->isPost()) {
            $params = [
                'form_data' => [
                    'system'         => $this->request->post('system'),
                    'website'        => $this->request->post('website'),
                    'lang_switch_on' => $this->request->post('lang_switch_on/f'),
                    '__token__'      => $this->request->post('__token__'),
                ],
            ];

            $result = action('Lang/saveLangConfig', $params, 'controller\settings');

            $this->showMessage($result, lang('save success'));
        }

        $result = action('Lang/getLangConfig', [], 'controller\settings');
        $this->assign('json_data_lang', json_encode($result));
        return $this->fetch();
    }

    /**
     * 图片设置
     * @access public
     * @param
     * @return mixed
     */
    public function image()
    {
        if ($this->request->isPost()) {
            $params = [
                'form_data' => [
                    'auto_image'             => $this->request->post('auto_image/f'),
                    'article_module_width'   => $this->request->post('article_module_width/f'),
                    'article_module_height'  => $this->request->post('article_module_height/f'),
                    'picture_module_width'   => $this->request->post('picture_module_width/f'),
                    'picture_module_height'  => $this->request->post('picture_module_height/f'),
                    'download_module_width'  => $this->request->post('download_module_width/f'),
                    'download_module_height' => $this->request->post('download_module_height/f'),
                    'page_module_width'      => $this->request->post('page_module_width/f'),
                    'page_module_height'     => $this->request->post('page_module_height/f'),
                    'product_module_width'   => $this->request->post('product_module_width/f'),
                    'product_module_height'  => $this->request->post('product_module_height/f'),
                    'job_module_width'       => $this->request->post('job_module_width/f'),
                    'job_module_height'      => $this->request->post('job_module_height/f'),
                    'link_module_width'      => $this->request->post('link_module_width/f'),
                    'link_module_height'     => $this->request->post('link_module_height/f'),
                    'ask_module_width'       => $this->request->post('ask_module_width/f'),
                    'ask_module_height'      => $this->request->post('ask_module_height/f'),
                    'add_water'              => $this->request->post('add_water/f'),
                    'water_type'             => $this->request->post('water_type/f'),
                    'water_location'         => $this->request->post('water_location/f'),
                    'water_text'             => $this->request->post('water_text'),
                    'water_image'            => $this->request->post('water_image'),
                    '__token__'              => $this->request->post('__token__'),
                ],
            ];

            $result = action('Image/saveImageConfig', $params, 'controller\settings');

            $this->showMessage($result, lang('save success'));
        }

        $result = action('Image/getImageConfig', [], 'controller\settings');
        $this->assign('json_data_image', json_encode($result));
        return $this->fetch();
    }

    /**
     * 安全与效率设置
     * @access public
     * @param
     * @return mixed
     */
    public function safe()
    {
        if ($this->request->isPost()) {
            $params = [
                'form_data' => [
                    'content_check'          => $this->request->post('content_check/f'),
                    'member_login_captcha'   => $this->request->post('member_login_captcha/f'),
                    'website_submit_captcha' => $this->request->post('website_submit_captcha/f'),
                    'website_static'         => $this->request->post('website_static/f'),
                    'upload_file_max'        => $this->request->post('upload_file_max/f'),
                    'upload_file_type'       => $this->request->post('upload_file_type'),
                    '__token__'              => $this->request->post('__token__'),
                ],
            ];

            $result = action('Safe/saveSafeConfig', $params, 'controller\settings');

            $this->showMessage($result, lang('save success'));
        }

        $result = action('Safe/getSafeConfig', [], 'controller\settings');
        $this->assign('json_data_safe', json_encode($result));
        return $this->fetch();
    }

    /**
     * 邮箱设置
     * @access public
     * @param
     * @return mixed
     */
    public function email()
    {
        if ($this->request->isPost()) {
            $params = [
                'form_data' => [
                    'smtp_host'       => $this->request->post('smtp_host'),
                    'smtp_port'       => $this->request->post('smtp_port/f'),
                    'smtp_username'   => $this->request->post('smtp_username'),
                    'smtp_password'   => $this->request->post('smtp_password'),
                    'smtp_from_email' => $this->request->post('smtp_from_email'),
                    'smtp_from_name'  => $this->request->post('smtp_from_name'),
                    '__token__'       => $this->request->post('__token__'),
                ],
            ];

            $result = action('Email/saveEmailConfig', $params, 'controller\settings');

            $this->showMessage($result, lang('save success'));
        }

        $result = action('Email/getEmailConfig', [], 'controller\settings');
        $this->assign('json_data_email', json_encode($result));
        return $this->fetch();
    }
}
