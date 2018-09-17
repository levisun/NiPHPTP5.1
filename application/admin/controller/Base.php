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
        // 生成异步请求令牌
        logic('common/Async')->createRequireToken();

        // IP地区信息[记录自己的IP地址库]
        logic('common/IpInfo')->getInfo();

        // 请求参数
        $this->requestParam = [
            'module'     => strtolower($this->request->module()),               // 请求模块
            'controller' => strtolower($this->request->controller()),           // 请求控制器
            'action'     => strtolower($this->request->action()),               // 请求方法
            'lang'       => lang(':detect'),                                    // 语言
        ];

        // 域名
        $this->domain = $this->request->domain() . $this->request->root() . '/';

        $this->setTemplate();

        // 用户权限校验
        if (session('?' . config('user_auth_key'))) {
            // 审核用户权限
            $auth =
            !logic('common/Rbac')
            ->checkAuth(
                session(config('user_auth_key')),
                $this->requestParam['module'],
                $this->requestParam['controller'],
                $this->requestParam['action']
            );
            if ($auth) {
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
        $template = get_template_config(config('default_theme'));

        config('template.view_path', $template['view_path']);

        $this->engine($template);
        $this->filter('view_filter');
    }
}
