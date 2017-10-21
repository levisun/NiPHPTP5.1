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
        $result = action('Basic/editor', [], 'controller\settings');
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data_basic', json_encode($result));
            return $this->fetch();
        }
    }

    /**
     * 语言设置
     * @access public
     * @param
     * @return mixed
     */
    public function lang()
    {
        $result = action('Lang/editor', [], 'controller\settings');
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data_lang', json_encode($result));
            return $this->fetch();
        }
    }

    /**
     * 图片设置
     * @access public
     * @param
     * @return mixed
     */
    public function image()
    {
        $result = action('Image/editor', [], 'controller\settings');
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data_image', json_encode($result));
            return $this->fetch();
        }
    }

    /**
     * 安全与效率设置
     * @access public
     * @param
     * @return mixed
     */
    public function safe()
    {
        $result = action('Safe/editor', [], 'controller\settings');
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data_safe', json_encode($result));
            return $this->fetch();
        }
    }

    /**
     * 邮箱设置
     * @access public
     * @param
     * @return mixed
     */
    public function email()
    {
        $result = action('Email/editor', [], 'controller\settings');
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data_email', json_encode($result));
            return $this->fetch();
        }
    }
}
