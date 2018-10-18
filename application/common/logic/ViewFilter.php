<?php
/**
 *
 * 模板过滤 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/10
 */
namespace app\common\logic;

class ViewFilter
{
    // 网站信息
    private $siteInfo;
    // 模板配置
    private $config;
    private $template;

    function __construct()
    {
        $this->siteInfo = logic('cms/siteinfo')->query();
        $this->template = config('template.');

        $this->analysisConfig();
    }

    public function view($_content)
    {
        $_content = preg_replace([
            '/<(\!DOCTYPE.*?)>(.*?)<(body.*?)>/si',
            '/<(\/body.*?)>(.*?)<(\/html.*?)>/si',
            '/<\!--.*?-->/si',                      // HTML注释
            '/(\/\*).*?(\*\/)/si',                  // JS注释
            '/(\r|\n| )+(\/\/).*?(\r|\n)+/si',      // JS注释
            '/( ){2,}/si',                          // 空格
            '/(\r|\n|\f)/si'                        // 回车
        ], '', $_content);
        $_content = $this->head($_content);
        $_content = $this->foot($_content);

        behavior(['app\\cms\\behavior\\HtmlCache', 'write'], $_content);

        return $_content;
    }

    /**
     * HTML底部信息
     * 用于扩展插件加载
     * @access private
     * @param  string $_content
     * @return string
     */
    private function foot($_content)
    {
        $foot  = '<script type="text/javascript">';
        $foot .= '';
        $foot .= 'console.log("author 失眠小枕头\ncopyright © 2013-' . date('Y') . ' by 失眠小枕头");';
        $foot .= '</script>';
        $foot .= '</body><html>';

        return $_content . $foot;
    }

    /**
     * HTML头部信息
     * @access private
     * @param  string $_content
     * @return string
     */
    private function head($_content)
    {
        $head = '<!DOCTYPE html><html lang="en"><head>' .
                '<meta charset="utf-8">' .
                '<meta name="author" content="失眠小枕头">' .
                '<meta name="generator" content="niphp">' .
                '<meta name="robots" content="all">' .
                '<meta name="renderer" content="webkit">' .
                '<meta http-equiv="Cache-Control" content="no-siteapp">' .
                '<title>' . $this->siteInfo['website_name'] . '</title>' .
                '<meta name="keywords" content="' . $this->siteInfo['website_keywords'] . '">' .
                '<meta name="description" content="' . $this->siteInfo['website_description'] . '">' .
                '<link rel="dns-prefetch" href="' . request()->domain() . '" />';

        if (request()->isMobile()) {
            $head .= '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">';
            $head .= '<meta name="apple-mobile-web-app-capable" content="yes">';
            $head .= '<meta name="apple-mobile-web-app-status-bar-style" content="black">';
            $head .= '<meta name="format-detection" content="telephone=yes">';
            $head .= '<meta name="format-detection" content="email=yes">';
        } else {
            $head .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
        }

        if (!empty($this->config['css'])) {
            foreach ($this->config['css'] as $css) {
                $head .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
            }
        }

        if (!empty($this->config['js'])) {
            foreach ($this->config['js'] as $js) {
                $head .= '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }

        $head .= '<script type="text/javascript">' .
                 'var request = {' .
                     'domain: "' . $this->template['tpl_replace_string']['__DOMAIN__'] . '",' .
                     'api: {' .
                        'query: "' . url('api/query', '', true, true) . '",' .
                        'settle: "' . url('api/settle', '', true, true) . '",' .
                        'upload: "' . url('api/upload', '', true, true) . '",' .
                     '},' .
                     'static: "' . $this->template['tpl_replace_string']['__STATIC__'] . '",' .
                     'css: "' . $this->template['tpl_replace_string']['__CSS__'] . '",' .
                     'js: "' . $this->template['tpl_replace_string']['__JS__'] . '",' .
                     'img: "' . $this->template['tpl_replace_string']['__IMG__'] . '"' .
                 '};' .
                 '</script>';

        $head .= '</head><body>';

        return $head . $_content;
    }

    /**
     * 解析模板配置文件
     * @access private
     * @param
     * @return void
     */
    private function analysisConfig()
    {
        if (!is_file(config('template.view_path') . 'config.json')) {
            return false;
        }

        $this->config = file_get_contents(config('template.view_path') . 'config.json');
        if (empty($this->config)) {
            return false;
        }

        $this->config = str_replace(
            array_keys($this->template['tpl_replace_string']),
            array_values($this->template['tpl_replace_string']),
            $this->config
        );
        $this->config = json_decode($this->config, true);
    }
}
