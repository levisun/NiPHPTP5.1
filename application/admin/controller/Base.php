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
 * @since     2017/09/13
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Env;
use think\facade\Lang;

class Base extends Controller
{

    protected function initialize()
    {
        // 权限
        $this->auth();
        // 语言
        $this->lang();
        // 模板
        $this->theme();
    }

    /**
     * 显示提示信息
     * @access protected
     * @param  bool|srting $resutn_data 返回数据
     * @param  string      $msg         提示信息
     * @param  string      $url         跳转链接
     * @return void
     */
    protected function showMessage($return_data, $msg = '', $url = null)
    {
        if (true === $return_data) {
            $this->success($msg, $url);
        } else {
            $this->error($return_data);
        }
    }

    /**
     * 权限
     * @access protected
     * @param
     * @return void
     */
    private function auth()
    {
        if (session('?' . config('user_auth_key'))) {
            $rbac = logic('Rbac', 'logic\account');
            if (!$rbac->checkAuth(session(config('user_auth_key')))) {
                $this->error('no permission', 'settings/info');
            }
        } elseif ($this->request->controller() != 'Account') {
            $this->redirect(url('account/login'));
        }
    }

    /**
     * 加载语言包
     * @access protected
     * @param
     * @return void
     */
    private function lang()
    {
        $lang_path  = Env::get('app_path') . $this->request->module();
        $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
        $lang_path .= Lang::detect() . DIRECTORY_SEPARATOR;

        // 加载全局语言包
        Lang::load($lang_path . Lang::detect() . '.php');

        // 加载对应语言包
        $lang_name  = strtolower($this->request->controller()) . DIRECTORY_SEPARATOR;
        $lang_name .= strtolower($this->request->action());
        Lang::load($lang_path . $lang_name . '.php');
    }

    /**
     * 模版设置
     * @access protected
     * @param
     * @return void
     */
    private function theme()
    {
        $view_path  = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
        $view_path .= 'theme' . DIRECTORY_SEPARATOR . $this->request->module();
        $view_path .= DIRECTORY_SEPARATOR . config('default_theme') . DIRECTORY_SEPARATOR;

        $template = config('template.');
        $template['view_path'] = $view_path;
        $this->view->engine($template);

        // 默认跳转页面对应的模板文件
        $dispatch = [
            'dispatch_success_tmpl' => $view_path . 'dispatch_jump.html',
            'dispatch_error_tmpl'   => $view_path . 'dispatch_jump.html',
        ];
        config($dispatch, 'app');

        // 获得域名地址
        $domain  = $this->request->domain();
        $domain .= substr($this->request->baseFile(), 0, -9);
        $default_theme  = $domain . 'theme/' . $this->request->module();
        $default_theme .= '/'. config('default_theme') . '/';

        $replace = [
            '__DOMAIN__'   => $domain,
            '__PHP_SELF__' => basename($this->request->baseFile()),
            '__STATIC__'   => $domain . 'static/',
            '__THEME__'    => config('default_theme'),
            '__CSS__'      => $default_theme . 'static/css/',
            '__JS__'       => $default_theme . 'static/js/',
            '__IMG__'      => $default_theme . 'static/images/',
        ];

        // 注入常用模板变量
        $common = logic('Common', 'logic\account');
        $auth_data = $common->createSysData();

        $replace['__TITLE__'] = $auth_data['title'];
        if (!empty($auth_data['auth_menu'])) {
            $replace['__SUB_TITLE__']  = $auth_data['sub_title'];
            $replace['__BREADCRUMB__'] = $auth_data['breadcrumb'];

            $this->assign('__ADMIN_DATA__', $auth_data['admin_data']);
            $this->assign('__MENU__', $auth_data['auth_menu']);
        }

        $this->view->replace($replace);

        $this->assign('button_search', 0);
        $this->assign('button_added', 0);
    }
}
