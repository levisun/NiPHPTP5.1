<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHP
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
    protected $middleware = [
        'app\admin\middleware\Auth'
    ];

    protected function initialize()
    {
        // 模板设置
        $template = get_template_config(config('default_theme'));
        config('template.view_path', $template['view_path']);
        config('template.tpl_replace_string', $template['tpl_replace_string']);
        $this->engine($template);

        $assign = [];

        if (session('?' . config('user_auth_key'))) {
            // 用户信息
            $assign['ADMIN_DATA'] = session('admin_data');

            // 权限菜单
            $auth_menu = logic('admin/account/auth')->getMenu();
            $assign['AUTH_MENU'] = json_encode($auth_menu);

            // 搜索按钮状态
            $this->assign('button_search', 0);
        }

        // 网站标题与面包屑
        $tit_bre = logic('admin/account/auth')->getTitBre();

        $assign['TITLE']      = $tit_bre['title'];
        $assign['BREADCRUMB'] = $tit_bre['breadcrumb'];
        $assign['SUB_TITLE']  = $tit_bre['sub_title'];

        $this->assign('SITE_DATA', $assign);

        $this->filter(function($_content){
            // 网站标题与面包屑
            $tit_bre = logic('admin/account/auth')->getTitBre();
            return html_head_foot($tit_bre, $_content);
        });
    }
}
