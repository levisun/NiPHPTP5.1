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

use app\admin\controller\settings\Info as ControllerInfo;
use app\admin\controller\settings\Basic as ControllerBasic;
use app\admin\controller\settings\Lang as ControllerLang;
use app\admin\controller\settings\Image as ControllerImage;
use app\admin\controller\settings\Safe as ControllerSafe;
use app\admin\controller\settings\Email as ControllerEmail;

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
        $controller_info = new ControllerInfo;
        $result = $controller_info->info();
        $this->assign('json_data', json_encode($result));
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
        $controller_basic = new ControllerBasic;
        $result = $controller_basic->editor();
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data', json_encode($result));
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
        $controller_lang = new ControllerLang;
        $result = $controller_lang->editor();
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data', json_encode($result));
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
        $controller_image = new ControllerImage;
        $result = $controller_image->editor();
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data', json_encode($result));
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
        $controller_safe = new ControllerSafe;
        $result = $controller_safe->editor();
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data', json_encode($result));
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
        $controller_email = new ControllerEmail;
        $result = $controller_email->editor();
        if (!is_array($result)) {
            $this->showMessage($result, lang('save success'));
        } else {
            $this->assign('json_data', json_encode($result));
            return $this->fetch();
        }
    }
}
