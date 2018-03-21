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
        $view_path .= 'cms' . DIRECTORY_SEPARATOR;
        $view_path .= config('default_theme') . DIRECTORY_SEPARATOR;

        // 模板地址 带域名
        $default_theme  = $this->domain . 'theme/cms/';
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
    }
}
