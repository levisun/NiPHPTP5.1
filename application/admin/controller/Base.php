<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    // 请求参数
    protected $requestParam = [];

    protected $domain = '';

    protected function initialize()
    {
        // 清除运行垃圾文件
        remove_rundata();

        // 异步请求加密签名
        logic('common/async')->createSign();

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

        $this->setTemplate();
        lang(':load');

        // 用户权限校验
        if (session('?' . config('user_auth_key'))) {
            // 审核用户权限
            if (!logic('admin/account/Rbac')->checkAuth(session(config('user_auth_key')))) {
                $this->error('no permission', 'settings/info');
            }
            // 登录页重定向
            if ($this->requestParam['action'] == 'login') {
                $this->redirect(url('settings/info'));
            }
            // 用户信息
            $this->assign('ADMIN_DATA', session('admin_data'));

            // 权限菜单
            $auth_menu = logic('admin/account/auth')->getMenu();
            $this->assign('AUTH_MENU', json_encode($auth_menu));

            // 搜索按钮状态
            $this->assign('button_search', 0);
        } elseif ($this->requestParam['controller'] != 'account') {
            $this->redirect(url('account/login'));
        }

        // 网站标题与面包屑
        $tit_bre = logic('admin/account/auth')->getTitBre();
        $this->assign('TITLE', $tit_bre['title']);
        $this->assign('BREADCRUMB', $tit_bre['breadcrumb']);
        $this->assign('SUB_TITLE', $tit_bre['sub_title']);
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
        $view_path  = env('root_path') . basename($this->request->root());
        $view_path .= DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR;
        $view_path .= 'admin' . DIRECTORY_SEPARATOR;
        $view_path .= config('default_theme') . DIRECTORY_SEPARATOR;

        // 模板地址 带域名
        $default_theme  = $this->domain . 'theme/admin/';
        $default_theme .= config('default_theme') . '/';

        $replace = [
            '__DOMAIN__'   => $this->domain,
            '__PHP_SELF__' => basename($this->request->baseFile()),
            '__STATIC__'   => $this->domain . 'static/',
            '__THEME__'    => config('default_theme'),
            '__CSS__'      => $default_theme . 'css/',
            '__JS__'       => $default_theme . 'js/',
            '__IMG__'      => $default_theme . 'images/',
        ];

        $template = config('template.');
        $template['view_path'] = $view_path;
        $template['tpl_replace_string'] = $replace;
        config('template.view_path', $view_path);
        config('template.tpl_replace_string', $replace);

        $this->view->engine($template);
        $this->view->filter('view_filter');
    }
}
