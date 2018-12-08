<?php
/**
 *
 * 模板过滤 - 业务层
 *
 * @package   NiPHP
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
        $foot .= 'console.log("Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com';
        $foot .= '\r\nAuthor: 失眠小枕头 levisun.mail@gmail.com';
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
        $cdn = request()->rootDomain() . request()->root() . '/';

        $head = '<!DOCTYPE html>' . PHP_EOL .
                '<html lang="en">' . PHP_EOL .
                '<head>' . PHP_EOL .
                '<meta charset="utf-8" />' . PHP_EOL .
                '<title>' . $this->siteInfo['title'] . '</title>' . PHP_EOL .
                '<meta name="generator" content="NiPHP ' . NP_VERSION . '" />' . PHP_EOL .
                '<meta name="author" content="失眠小枕头 levisun.mail@gmail.com" />' . PHP_EOL .
                '<meta name="copyright" content="2013-' . date('Y') . ' NiPHP 失眠小枕头" />' . PHP_EOL .
                '<meta name="robots" content="all" />' . PHP_EOL .
                '<meta name="revisit-after" content="1 days" >' . PHP_EOL .
                '<meta name="renderer" content="webkit" />' . PHP_EOL .
                '<meta name="force-rendering" content="webkit" />' . PHP_EOL .
                '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no" />' . PHP_EOL .
                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL .
                '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' . PHP_EOL .
                '<meta http-equiv="Cache-Control" content="no-siteapp" />' . PHP_EOL .
                '<meta http-equiv="Widow-target" content="_top">' . PHP_EOL .
                '<link rel="dns-prefetch" href="//cdn.' . $cdn . '" />' . PHP_EOL .
                '<link rel="dns-prefetch" href="//css.' . $cdn . '" />' . PHP_EOL .
                '<link rel="dns-prefetch" href="//js.' . $cdn . '" />' . PHP_EOL .
                '<link rel="dns-prefetch" href="//img.' . $cdn . '" />' . PHP_EOL;

        if (request()->isMobile()) {
            $head .= '<meta name="apple-mobile-web-app-capable" content="yes" />' . PHP_EOL .
                     '<meta name="apple-mobile-web-app-status-bar-style" content="black" />' . PHP_EOL .
                     '<meta name="format-detection" content="telephone=yes" />' . PHP_EOL .
                     '<meta name="format-detection" content="email=yes" />' . PHP_EOL;
        }

        $head .= '<meta name="keywords" content="' . $this->siteInfo['website_keywords'] . '" />' . PHP_EOL .
                 '<meta name="description" content="' . $this->siteInfo['website_description'] . '" />' . PHP_EOL .
                 '<meta property="og:site_name" content="' . $this->siteInfo['website_name'] . '" />' . PHP_EOL .
                 '<meta property="og:type" content="blog" />' . PHP_EOL .
                 '<meta property="og:title" content="' . $this->siteInfo['title'] . '" />' . PHP_EOL .
                 '<meta property="og:url" content="' . request()->url(true) . '" />' . PHP_EOL .
                 '<meta property="og:description" content="' . $this->siteInfo['website_description'] . '" />' . PHP_EOL .
                 '<link href="//cdn.' . $cdn . 'favicon.ico" rel="shortcut icon" type="image/x-icon" />' . PHP_EOL;

        if (!empty($this->config['css'])) {
            foreach ($this->config['css'] as $css) {
                $head .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />' . PHP_EOL;
            }
        }

        if (!empty($this->config['js'])) {
            foreach ($this->config['js'] as $js) {
                $head .= '<script type="text/javascript" src="' . $js . '"></script>' . PHP_EOL;
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
