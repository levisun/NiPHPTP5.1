<?php
/**
 *
 * 模板过滤 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\common\logic
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
        $this->siteInfo = logic(request()->module() . '/siteinfo')->query();
        $this->template = config('template.');

        $this->analysisConfig();
    }

    public function view($_content)
    {
        $_content = preg_replace([
            '/<(\!DOCTYPE.*?)>(.*?)<(body.*?)>/si',
            '/<(\/body.*?)>(.*?)<(\/html.*?)>/si',
            '/( ){2,}/si',
            '/(<\!--)(.*?)(-->)/si',
            '/(\/\*)(.*?)(\*\/)/si',
            '/(\r\n){2,}/si',
        ], '', $_content);
        $_content = $this->head($_content);
        $_content = $this->foot($_content);

        // logic('common/html')->write($_content);

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
        $foot  = '';

        // 插件加载
        if (!empty($this->config['hook'])) {
            foreach ($this->config['hook'] as $hook) {

            }
        }

        $foot .= PHP_EOL;
        $foot .= '<script type="text/javascript">';
        $foot .= 'console.log("Powered by NiPHP Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com';
        $foot .= '\r\nAuthor: NiPHP 失眠小枕头';
        $foot .= '\r\nCreate Date: ' . date('Y-m-d H:i:s');
        $foot .= '\r\nRuntime: ' . number_format(microtime(true) - app()->getBeginTime(), 6) . '秒';
        $foot .= '\r\nMemory: ' . number_format((memory_get_usage() - app()->getBeginMem()) / 1048576, 2) . 'MB");';
        $foot .= '</script>';
        $foot .= PHP_EOL . '</body>' . PHP_EOL . '</html>';

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
        $head = '<!DOCTYPE html>' .
                '<html lang="en">' .
                '<head>' .
                '<meta charset="utf-8" />';

        if (request()->isMobile()) {
            $head .= '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no" />';
            $head .= '<meta name="apple-mobile-web-app-capable" content="yes" />';
            $head .= '<meta name="apple-mobile-web-app-status-bar-style" content="black" />';
            $head .= '<meta name="format-detection" content="telephone=yes" />';
            $head .= '<meta name="format-detection" content="email=yes" />';
        } else {
            $head .= '<meta name="renderer" content="webkit" />';
            $head .= '<meta name="force-rendering" content="webkit" />';
            $head .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
        }

        $head .= '<meta name="generator" content="NiPHP ' . NP_VERSION . '" />' .
                 '<meta name="author" content="NiPHP 失眠小枕头" />' .
                 '<meta name="copyright" content="2013-' . date('Y') . ' NiPHP 失眠小枕头" />' .
                 '<meta name="robots" content="all" />' .

                 '<meta http-equiv="Cache-Control" content="no-siteapp" />' .
                 '<title>' . $this->siteInfo['title'] . '</title>' .
                 '<meta name="keywords" content="' . $this->siteInfo['website_keywords'] . '" />' .
                 '<meta name="description" content="' . $this->siteInfo['website_description'] . '" />' .
                 '<meta property="og:site_name" content="' . $this->siteInfo['website_name'] . '" />' .
                 '<meta property="og:type" content="blog" />' .
                 '<meta property="og:title" content="' . $this->siteInfo['title'] . '" />' .
                 '<meta property="og:url" content="' . request()->url(true) . '" />' .
                 '<meta property="og:description" content="' . $this->siteInfo['website_description'] . '" />' .
                 '<link rel="dns-prefetch" href="' . $this->template['tpl_replace_string']['__CDN__'] . '" />' .
                 '<link href="' . request()->domain() . '/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

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
                        'query: "' . url('api/query') . '",' .
                        'settle: "' . url('api/settle') . '",' .
                        'upload: "' . url('api/upload') . '",' .
                     '},' .
                     'static: "' . $this->template['tpl_replace_string']['__STATIC__'] . '",' .
                     'css: "' . $this->template['tpl_replace_string']['__CSS__'] . '",' .
                     'js: "' . $this->template['tpl_replace_string']['__JS__'] . '",' .
                     'img: "' . $this->template['tpl_replace_string']['__IMG__'] . '"' .
                 '};' .
                 '</script>';

        $head .= '</head><body>' . PHP_EOL;

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
