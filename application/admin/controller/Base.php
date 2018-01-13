<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Base.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Env;

class Base extends Controller
{
    // 请求参数
    protected $requestParam = [];

    protected $domain = '';

    protected function initialize()
    {
        // 请求参数
        $this->requestParam = [
            // 请求模块
            'module'     => strtolower($this->request->module()),
            // 请求控制器
            'controller' => strtolower($this->request->controller()),
            // 请求方法
            'action'     => strtolower($this->request->action()),
            // 语言
            'lang'       => lang(':detect'),
        ];

        // 域名
        $this->domain = $this->request->domain() . $this->request->root() . '/';

        // 用户权限校验
        if (session('?' . config('user_auth_key'))) {
            // 审核用户权限
            if (!logic('admin/Rbac', 'account')->checkAuth(session(config('user_auth_key')))) {
                $this->error('no permission', 'settings/info');
            }
            // 登录页重定向
            if ($this->requestParam['action'] == 'login') {
                $this->redirect(url('settings/info'));
            }
        } elseif ($this->requestParam['controller'] != 'account') {
            $this->redirect(url('account/login'));
        }

        $this->setTemplate();

        cookie('__sign', encrypt($this->requestParam['module'] . '.' . time()));
    }

    /**
     * 设置模板
     * @access private
     * @param
     * @return void
     */
    private function setTemplate()
    {
        // 重新定义模板目录
        $view_path  = Env::get('root_path') . basename($this->request->root());
        $view_path .= DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR;
        $view_path .= $this->request->module() . DIRECTORY_SEPARATOR;
        $view_path .= config('default_theme') . DIRECTORY_SEPARATOR;

        // 模板地址 带域名
        $default_theme  = $this->domain . 'theme/' . $this->request->module();
        $default_theme .= '/'. config('default_theme') . '/';

        $replace = [
            '__DOMAIN__'   => $this->domain,
            '__PHP_SELF__' => basename($this->request->baseFile()),
            '__STATIC__'   => $this->domain . 'static/',
            '__THEME__'    => config('default_theme'),
            '__CSS__'      => $default_theme . 'static/css/',
            '__JS__'       => $default_theme . 'static/js/',
            '__IMG__'      => $default_theme . 'static/images/',
        ];

        $template = config('template.');
        $template['view_path'] = $view_path;
        $template['tpl_replace_string'] = $replace;
        config('template.view_path', $view_path);
        config('template.tpl_replace_string', $replace);

        $this->view->engine($template);
    }
}