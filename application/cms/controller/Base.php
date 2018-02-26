<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index
{

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
        $common = logic('Common', 'logic\common');
        $auth_data = $common->createSysData();

        $replace['__TITLE__'] = $auth_data['title'];

        $this->view->replace($replace);

        $this->assign('request_param', json_encode($this->requestParam));
    }
}
