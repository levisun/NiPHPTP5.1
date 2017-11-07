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

use app\admin\logic\settings\Info as LogicInfo;
use app\admin\logic\settings\Basic as LogicBasic;
use app\admin\logic\settings\Lang as LogicLang;
use app\admin\logic\settings\Image as LogicImage;
use app\admin\logic\settings\Safe as LogicSafe;
use app\admin\logic\settings\Email as LogicEmail;

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
        $controller_info = new LogicInfo;
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
        $logic_basic = new LogicBasic;
        if ($this->request->isPost()) {
            $result = $logic_basic->update();
            $this->showMessage($result, lang('save success'));
        } else {
            $result = $logic_basic->getBasicConfig();
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
        $logic_lang = new LogicLang;
        if ($this->request->isPost()) {
            $result = $logic_lang->update();
            $this->showMessage($result, lang('save success'));
        } else {
            $result = $logic_lang->getLangConfig();
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
        $logic_image = new LogicImage;
        if ($this->request->isPost()) {
            $result = $logic_image->update();
            $this->showMessage($result, lang('save success'));
        } else {
            $result = $logic_image->getImageConfig();
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
        $logic_safe = new LogicSafe;
        if ($this->request->isPost()) {
            $result = $logic_safe->update();
            $this->showMessage($result, lang('save success'));
        } else {
            $result = $logic_safe->getSafeConfig();
            $this->assign('json_data', json_encode($result));
            return $this->fetch();
        }

        $controller_safe = new LogicSafe;
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
        $logice_mail = new LogicEmail;
        if ($this->request->isPost()) {
            $result = $logice_mail->update();
            $this->showMessage($result, lang('save success'));
        } else {
            $result = $logice_mail->getEmailConfig();
            $this->assign('json_data', json_encode($result));
            return $this->fetch();
        }
    }
}
