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
            $result = action('Basic/saveBasicConfig', [], 'controller\settings');
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
            $result = action('Lang/saveLangConfig', [], 'controller\settings');
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
            $result = action('Safe/saveSafeConfig', [], 'controller\settings');
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
            $result = action('Email/saveEmailConfig', [], 'controller\settings');
            $this->showMessage($result, lang('save success'));
        }

        $result = action('Email/getEmailConfig', [], 'controller\settings');
        $this->assign('json_data_email', json_encode($result));
        return $this->fetch();
    }
}
