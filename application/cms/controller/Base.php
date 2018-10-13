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

    protected $siteInfo = [];

    protected function initialize()
    {
        $this->siteInfo = logic('cms/siteinfo')->query();

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
        $template['tpl_replace_string']['__BOTTOM_MSG__']  = htmlspecialchars_decode($this->siteInfo['bottom_message']);
        $template['tpl_replace_string']['__COPYRIGHT__']   = $this->siteInfo['copyright'];
        $template['tpl_replace_string']['__SCRIPT__']      = htmlspecialchars_decode($this->siteInfo['script']);

        config('template.view_path', $template['view_path']);

        $this->engine($template);
        $this->assign('KEYWORDS', $this->siteInfo['website_keywords']);
        $this->assign('DESCRIPTION', $this->siteInfo['website_description']);

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
