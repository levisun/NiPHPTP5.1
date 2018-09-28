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

    protected $siteInfo = [];

    protected $tableName = '';

    protected function initialize()
    {
        // 生成异步请求令牌
        logic('common/async')->createRequireToken();

        // 请求参数
        $this->requestParam = [
            'module'     => strtolower($this->request->module()),               // 请求模块
            'controller' => strtolower($this->request->controller()),           // 请求控制器
            'action'     => strtolower($this->request->action()),               // 请求方法
            'lang'       => lang(':detect'),                                    // 语言
        ];

        $this->siteInfo = logic('cms/siteinfo')->query();

        $this->setTemplate();

        if ($this->requestParam['action'] != 'index' && !$this->tableName = logic('cms/article')->queryTableName()) {
            // abort(404);
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
        ->where([
            ['name', '=', 'cms_theme'],
            ['lang', '=', lang(':detect')],
        ])
        ->cache('CMS BASE SETTEMPLATE')
        ->value('value');

        $template = get_template_config($default_theme);

        $template['taglib_pre_load'] = 'app\cms\taglib\Label';

        $template['tpl_replace_string']['__TITLE__']       = $this->siteInfo['website_name'];
        $template['tpl_replace_string']['__KEYWORDS__']    = $this->siteInfo['website_keywords'];
        $template['tpl_replace_string']['__DESCRIPTION__'] = $this->siteInfo['website_description'];
        $template['tpl_replace_string']['__BOTTOM_MSG__']  = htmlspecialchars_decode($this->siteInfo['bottom_message']);
        $template['tpl_replace_string']['__COPYRIGHT__']   = $this->siteInfo['copyright'];
        $template['tpl_replace_string']['__SCRIPT__']      = htmlspecialchars_decode($this->siteInfo['script']);

        config('template.view_path', $template['view_path']);

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
