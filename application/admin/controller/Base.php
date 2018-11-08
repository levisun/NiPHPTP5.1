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

    protected function initialize()
    {
        // 模板设置
        $template = get_template_config(config('default_theme'));
        config('template.view_path', $template['view_path']);
        $this->engine($template);
        $this->filter('view_filter');

        if (session('?' . config('user_auth_key'))) {
            // 用户信息
            $this->assign('ADMIN_DATA', session('admin_data'));

            // 权限菜单
            $auth_menu = logic('admin/account/auth')->getMenu();
            $this->assign('AUTH_MENU', json_encode($auth_menu));

            // 搜索按钮状态
            $this->assign('button_search', 0);
        }

        // 网站标题与面包屑
        $tit_bre = logic('admin/account/auth')->getTitBre();
        $this->assign('TITLE', $tit_bre['title']);
        $this->assign('BREADCRUMB', $tit_bre['breadcrumb']);
        $this->assign('SUB_TITLE', $tit_bre['sub_title']);
    }
}
