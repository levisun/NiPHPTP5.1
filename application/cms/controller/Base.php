<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

use think\Controller;

class Base extends Controller
{
    // 请求参数
    protected $requestParam = [];

    protected $domain = '';

    protected $siteInfo = [];

    protected $tableName = '';

    protected function initialize()
    {
        concurrent_error();

        // 生成异步请求令牌
        logic('common/async')->createRequireToken();
        // IP地区信息[记录自己的IP地址库]
        logic('common/IpInfo')->getInfo();
        // 访问记录
        logic('common/visit')->addedVisit();
        logic('common/visit')->addedSearchengine();

        // 请求参数
        $this->requestParam = [
            'module'     => strtolower($this->request->module()),               // 请求模块
            'controller' => strtolower($this->request->controller()),           // 请求控制器
            'action'     => strtolower($this->request->action()),               // 请求方法
            'lang'       => lang(':detect'),                                    // 语言
        ];

        // 域名
        $this->domain = $this->request->domain() . $this->request->root() . '/';

        $this->siteInfo = logic('cms/siteinfo')->query();

        $this->setTemplate();

        if ($this->requestParam['action'] != 'index' && !$this->tableName = logic('cms/article')->queryTableName()) {
            $this->redirect(url('error/page', ['code' => 404], 'html', true));
        }
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
        $default_theme =
        model('common/config')
        ->field(true)
        ->where([
            ['name', '=', 'cms_theme'],
            ['lang', '=', lang(':detect')],
        ])
        ->value('value');

        $view_path  = env('root_path') . basename($this->request->root());
        $view_path .= DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR;
        $view_path .= 'cms' . DIRECTORY_SEPARATOR;
        $view_path .= $default_theme . DIRECTORY_SEPARATOR;

        // 模板地址 带域名
        $default_theme  = $this->domain . 'theme/cms/' . $default_theme . '/';

        $replace = [
            '__DOMAIN__'      => $this->domain,
            '__PHP_SELF__'    => basename($this->request->baseFile()),
            '__STATIC__'      => $this->domain . 'static/',
            '__THEME__'       => config('default_theme'),
            '__CSS__'         => $default_theme . 'css/',
            '__JS__'          => $default_theme . 'js/',
            '__IMG__'         => $default_theme . 'images/',

            '__TITLE__'       => $this->siteInfo['website_name'],
            '__KEYWORDS__'    => $this->siteInfo['website_keywords'],
            '__DESCRIPTION__' => $this->siteInfo['website_description'],
            '__BOTTOM_MSG__'  => htmlspecialchars_decode($this->siteInfo['bottom_message']),
            '__COPYRIGHT__'   => $this->siteInfo['copyright'],
            '__SCRIPT__'      => htmlspecialchars_decode($this->siteInfo['script']),
        ];

        $template = config('template.');
        $template['view_path'] = $view_path;
        $template['tpl_replace_string'] = $replace;
        $template['taglib_pre_load'] = 'app\cms\taglib\Label';
        config('template.view_path', $view_path);
        config('template.tpl_replace_string', $replace);

        $this->engine($template);
        $this->filter('view_filter');
    }

    /**
     * 加载模板输出
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        if ($template) {
            $template = config('template.view_path') . $template;
        }
        return parent::fetch($template, $vars, $config);
    }
}
