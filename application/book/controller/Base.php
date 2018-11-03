<?php
/**
 *
 * 全局 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\book\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\book\controller;

use think\Controller;

class Base extends Controller
{

    protected $siteInfo = [];

    protected function initialize()
    {
        $this->siteInfo = logic('book/siteinfo')->query();

        $template = get_template_config($this->siteInfo['cms_theme']);

        $template['taglib_pre_load'] = 'app\book\taglib\Label';
        $template['tpl_replace_string']['__TITLE__']      = $this->siteInfo['website_name'];
        $template['tpl_replace_string']['__BOTTOM_MSG__'] = htmlspecialchars_decode($this->siteInfo['bottom_message']);
        $template['tpl_replace_string']['__COPYRIGHT__']  = $this->siteInfo['copyright'];
        $template['tpl_replace_string']['__SCRIPT__']     = htmlspecialchars_decode($this->siteInfo['script']);

        config('template.view_path', $template['view_path']);
        config('template.tpl_replace_string', $template['tpl_replace_string']);

        $this->engine($template);

        $view_filter = logic('common/ViewFilter');
        $this->filter([$view_filter, 'view']);
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

        return parent::fetch($template . '.' . config('template.view_suffix'), $vars, $config);
    }
}
