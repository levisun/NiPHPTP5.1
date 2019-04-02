<?php
/**
 *
 * 服务层
 * 模板类
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\Container;
use think\Response;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;
use app\library\Base64;
use app\library\Tags;

class Template
{
    /**
     * 模板路径
     * @var string
     */
    private $templatePath;

    /**
     * 模板路径
     * @var string
     */
    private $buildPath;

    /**
     * 主题
     * @var string
     */
    public $theme = '';

    /**
     * 替换变量
     * @var string
     */
    public $templateReplace = [
        '{:__CSS__}'         => '',
        '{:__IMG__}'         => '',
        '{:__JS__}'          => '',
        '{:__STATIC___}'     => '',
        '{:__TITLE__}'       => '',
        '{:__KEYWORDS__}'    => '',
        '{:__DESCRIPTION__}' => '',
        '{:__BOTTOM_MSG__}'  => '',
        '{:__COPYRIGHT__}'   => '',
    ];

    /**
     * 模板配置
     * @var string
     */
    private $config = [];

    public $varData = [];

    /**
     * 架构函数
     * @access public
     * @param  array $config
     */
    public function __construct()
    {
        $this->templatePath  = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
        $this->templatePath .= 'template' . DIRECTORY_SEPARATOR;

        $this->buildPath  = app()->getRuntimePath() . 'html' . Base64::flag() . DIRECTORY_SEPARATOR;
        $this->buildPath .= Request::subDomain() . DIRECTORY_SEPARATOR;
        $this->buildPath .= Lang::detect() . DIRECTORY_SEPARATOR;
    }

    public function assign(array $_vars = [])
    {
        $this->varData = array_merge($this->varData, $_vars);
        return $this;
    }

    public function fetch(string $_template = '')
    {
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        if (!$content = $this->templateBuildRead()) {
            $this->templateConfig = $this->parseTemplateConfig();
            $content = file_get_contents($this->parseTemplateFile($_template));
            $content = $this->parseTemplateLayout($content);
            $content = $this->parseTemplateInclude($content);
            $content = $this->parseTemplateReplace($content);
            $content = $this->parseTemplateVars($content);
            // $content = $this->parseTemplateTags($content);

            // 去除html空格与换行与PHP代码安全
            $content = Filter::ENTER($content);
            $content = Filter::FUN($content);

            $content = $this->parseTemplateHead() .
                       $content .
                       $this->parseTemplateFoot();

            $content .= '<!-- ' . json_encode([
                'static' => !APP_DEBUG ? 'open' : 'close',
                'layout' => $this->templateConfig['layout'] ? 'open' : 'close',
                'theme'  => $this->theme . $_template,
                'date'   => date('Y-m-d H:i:s')
            ]) . ' -->';

            $this->templateBuildWrite($content);
        }


        echo str_replace('{:__AUTHORIZATION__}', createAuthorization(), $content);

        echo '<!-- Time:' . number_format(microtime(true) - Container::pull('app')->getBeginTime(), 6) . 's ';
        echo 'Memory:' . number_format((memory_get_usage() - Container::pull('app')->getBeginMem()) / 1024 / 1024, 2) . 'mb' . ' -->';

        // 获取并清空缓存
        $content = ob_get_clean();

        echo $content;

        clearstatcache();
    }

    /**
     * 解析foot
     * @access private
     * @param
     * @return string 底部HTML
     */
    private function parseTemplateFoot(): string
    {
        list($root) = explode('.', Request::rootDomain(), 2);

        $foot = '<script type="text/javascript">' .
        'var NICMS = {' .
            'domain:"' . '//' . Request::rootDomain() . Request::root() . '",' .
            'api:{' .
                'url:"' . Config::get('app.api_host') . '",'.
                'root:"' . $root . '",' .
                'version:"' . $this->templateConfig['api_version'] . '",' .
                'authorization:"{:__AUTHORIZATION__}"' .
            '},' .
            'cdn:{' .
                'css:"' . $this->templateReplace['{:__CSS__}'] . '",' .
                'img:"' . $this->templateReplace['{:__IMG__}'] . '",' .
                'js:"' . $this->templateReplace['{:__JS__}'] . '",' .
                'static:"' . $this->templateReplace['{:__STATIC__}'] . '",' .
            '},' .
            'url:"' . url() . '",' .
            'param:' . json_encode(Request::param()) . ',' .
            'window: {' .
                'width:document.documentElement.clientWidth,' .
                'height:document.documentElement.clientHeight' .
            '}' .
        '};' .
        '</script>';
        unset($root);

        if (!empty($this->templateConfig['js'])) {
            foreach ($this->templateConfig['js'] as $js) {
                // $script = file_get_contents($js);
                // $script = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);
                // $foot .= '<script type="text/javascript" class="' . md5($js) . '">' . $script . '</script>';
                $foot .= '<script type="text/javascript" src="' . $js . '?v=' . $this->templateConfig['theme_version'] . '"></script>';
            }
        }

        // 插件加载

        // 底部JS脚本
        $foot .= $this->templateReplace['{:__SCRIPT__}'];

        return $foot . '</body></html>';
    }

    /**
     * 解析head
     * @access private
     * @param
     * @return string 头部HTML
     */
    private function parseTemplateHead(): string
    {
        $head =
        '<!DOCTYPE html>' .
        '<html lang="' . Lang::detect() . '">' .
        '<head>' .
        '<meta charset="utf-8" />' .
        '<meta name="fragment" content="!" />' .                                // 支持蜘蛛ajax
        '<meta name="robots" content="all" />' .                                // 蜘蛛抓取
        '<meta name="revisit-after" content="1 days" />' .                      // 蜘蛛重访
        '<meta name="renderer" content="webkit" />' .                           // 强制使用webkit渲染
        '<meta name="force-rendering" content="webkit" />' .
        '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no" />' .

        '<meta name="generator" content="NiPHP" />' .
        '<meta name="author" content="失眠小枕头 levisun.mail@gmail.com" />' .
        '<meta name="copyright" content="2013-' . date('Y') . ' NiPHP 失眠小枕头" />' .

        '<meta http-equiv="Window-target" content="_blank">' .
        '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' .

        '<meta http-equiv="Cache-Control" content="no-siteapp" />' .            // 禁止baidu转码
        '<meta http-equiv="Cache-Control" content="no-transform" />' .

        '<meta http-equiv="x-dns-prefetch-control" content="on" />' .           // DNS缓存
        '<link rel="dns-prefetch" href="' . Config::get('app.api_host') . '" />' .
        '<link rel="dns-prefetch" href="' . Config::get('app.cdn_host') . '" />' .

        '<link href="' . Config::get('app.cdn_host') . '/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

        // 网站标题 关键词 描述
        $head .= '<title>' . $this->templateReplace['{:__TITLE__}'] . '</title>';
        $head .= '<meta name="keywords" content="' . $this->templateReplace['{:__KEYWORDS__}'] . '" />';
        $head .= '<meta name="description" content="' . $this->templateReplace['{:__DESCRIPTION__}'] . '" />';

        if (!empty($this->templateConfig['meta'])) {
            foreach ($this->templateConfig['meta'] as $m) {
                $head .= '<meta ' . $m['type'] . ' ' . $m['content'] . ' />';
            }
        }
        // <meta name="apple-itunes-app" content="app-id=1191720421, app-argument=sspai://sspai.com">

        if (!empty($this->templateConfig['css'])) {
            foreach ($this->templateConfig['css'] as $css) {
                // $style = file_get_contents($css);
                // $style = Filter::XSS($style);
                // $style = Filter::XXE($style);
                // $style = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $style);
                // $style = preg_replace('/(\n|\r)/si', '', $style);
                // $style = preg_replace('/( ){2,}/si', '', $style);
                // $head .= '<style type="text/css" class="' . md5($css) . '">' . $style . '</style>';
                $head .= '<link rel="stylesheet" type="text/css" href="' . $css . '?v=' . $this->templateConfig['theme_version'] . '" />';
            }
        }

        return $head . '</head><body>';
    }

    /**
     * 读取模板静态文件
     * @access private
     * @param
     * @return string HTML
     */
    private function templateBuildRead(): string
    {
        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('-', $url);
        $url = $url ? $url . '.html' : 'index.html';

        $expire = Config::get('cache.expire');
        if (APP_DEBUG === true) {
            return '';
        } elseif (is_file($this->buildPath . $url) && filemtime($this->buildPath . $url) >= time() - rand($expire, $expire*2)) {
            return file_get_contents($this->buildPath . $url);
        } else {
            return '';
        }
    }

    /**
     * 生成模板静态文件
     * @access private
     * @param  string $_content
     * @return void
     */
    private function templateBuildWrite(string $_content)
    {
        if (!is_dir($this->buildPath)) {
            chmod(app()->getRuntimePath(), 0777);
            mkdir($this->buildPath, 0777, true);
        }

        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('-', $url);
        $url = $url ? $url . '.html' : 'index.html';

        APP_DEBUG or file_put_contents($this->buildPath . $url, $_content);
    }

    /**
     * 解析模板变量
     * @access private
     * @param  string $_content
     * @return string content
     */
    private function parseTemplateTags(string $_content): string
    {
        if (preg_match_all('/({tag:[a-zA-Z_$=> ]+})(.*?)({\/tag})/si', $_content, $matches) !== false) {
            foreach ($matches[2] as $key => $tags) {
                print_r($matches[1][$key]);

                print_r($tags);
                print_r($matches[3][$key]);
            }
        }die();
        return $_content;
    }

    /**
     * 解析模板变量
     * @access private
     * @param  string $_content
     * @return string content
     */
    private function parseTemplateVars(string $_content): string
    {
        if (preg_match_all('/({:\$)([a-zA-Z_.]+)(})/si', $_content, $matches) !== false) {
            foreach ($matches[2] as $key => $var_name) {
                list($var_type, $vars) = explode('.', $var_name, 2);
                $var_type = strtoupper(trim($var_type));
                switch ($var_type) {
                    case 'SERVER':
                        $var_type = '$_SERVER';
                        $var_name = $vars;
                        break;

                    case 'GET':
                        $var_type = '$_GET';
                        $var_name = $vars;
                        break;

                    case 'POST':
                        $var_type = '$_POST';
                        $var_name = $vars;
                        break;

                    case 'COOKIE':
                        $var_type = '$_COOKIE';
                        $var_name = $vars;
                        break;

                    case 'SESSION':
                        $var_type = '$_SESSION';
                        $var_name = $vars;
                        break;

                    case 'ENV':
                        $var_type = '$_ENV';
                        $var_name = $vars;
                        break;

                    case 'REQUEST':
                        $var_type = '$_REQUEST';
                        $var_name = $vars;
                        break;

                    case 'CONST':
                        $var_type = 'CONST';
                        $var_name = strtoupper($vars);
                        break;

                    default:
                        $var_type = '$this->varData';
                        break;
                }
                unset($vars);

                if ($var_type === 'CONST') {
                    eval('$var_name = defined(\'' . $var_name . '\') ? ' . $var_name . ' : null;');
                } else {
                    $var_name = $var_type . '[\'' . str_replace('.', '\'][\'', $var_name) . '\']';
                    eval('$var_name = isset(' . $var_name . ') ? ' . $var_name . ' : null;');
                }
                if (is_null($var_name)) {
                    // throw new HttpException(200, '未定义!');
                }
                $_content = str_replace($matches[0][$key], $var_name, $_content);
                unset($var_type);
            }
        }

        return $_content;
    }

    /**
     * 解析模板引入文件
     * @access private
     * @param  string $_content 模板内容
     * @return string
     */
    private function parseTemplateInclude(string $_content): string
    {
        if (preg_match_all('/({:include file=["|\'])([a-zA-Z_]+)(["|\']})/si', $_content, $matches) !== false) {
            foreach ($matches[2] as $key => $value) {
                if ($value && is_file($this->templatePath . $this->theme . $value . '.html')) {
                    $file = file_get_contents($this->templatePath . $this->theme . $value . '.html');
                    $_content = str_replace($matches[0][$key], $file, $_content);
                }
            }
        }

        return $_content;
    }

    /**
     * 解析布局模板
     * @access private
     * @param  string $_template 模板名
     * @return string 模板路径
     */
    private function parseTemplateLayout(string $_content): string
    {
        if ($this->templateConfig['layout'] == true) {
            if (strpos($_content, '{:NOT_LAYOUT}') === false) {
                if (is_file($this->templatePath . $this->theme . 'layout.html')) {
                    $layout = file_get_contents($this->templatePath . $this->theme . 'layout.html');
                    $_content = str_replace('{:__CONTENT__}', $_content, $layout);
                } else {
                    throw new HttpException(200, '布局模板不存在.');
                }
            }
        }

        return $_content;
    }

    /**
     * 解析模板配置
     * @access private
     * @param  string $_template 模板名
     * @return string 模板路径
     */
    private function parseTemplateConfig(): array
    {
        if (is_file($this->templatePath . $this->theme . 'config.json')) {
            $config = file_get_contents($this->templatePath . $this->theme . 'config.json');
            $config = strip_tags($config);
            $config = $this->parseTemplateReplace($config);
            $config = json_decode($config, true);
            if (!empty($config) && is_array($config)) {
                $keys = array_keys($config);
                if (!in_array('layout', $keys)) {
                    throw new HttpException(200, '模板配置文件错误.');
                } elseif (!in_array('api_version', $keys)) {
                    throw new HttpException(200, '模板配置文件错误.');
                } elseif (!in_array('theme_version', $keys)) {
                    throw new HttpException(200, '模板配置文件错误.');
                }

                return $config;
            }
        }

        throw new HttpException(200, '模板配置文件错误.' . $this->theme);
    }

    /**
     * 解析模板替换字符
     * @access private
     * @param  string $_content 模板内容
     * @return string 模板路径
     */
    private function parseTemplateReplace(string $_content): string
    {
        if (!empty($this->templateReplace)) {
            $search = array_keys($this->templateReplace);
            $replace = array_values($this->templateReplace);

            $_content = trim($_content);
            $_content = str_replace($search, $replace, $_content);
        }

        return $_content;
    }

    /**
     * 解析模板路径
     * @access private
     * @param  string $_template 模板名
     * @return string 模板路径
     */
    private function parseTemplateFile(string $_template): string
    {
        $_template  = str_replace(['\\', ':'], DIRECTORY_SEPARATOR, $_template);
        $_template .= '.html';

        // 微信和移动端访问时,判断模板是否存在.
        if (isWechat() && is_file($this->templatePath . $this->theme . 'wechat' . DIRECTORY_SEPARATOR . $_template)) {
            $_template = 'wechat' . DIRECTORY_SEPARATOR . $_template;
        } elseif (Request::isMobile() && is_file($this->templatePath . $this->theme . 'mobile' . DIRECTORY_SEPARATOR . $_template)) {
            $_template = 'mobile' . DIRECTORY_SEPARATOR . $_template;
        }

        if (is_file($this->templatePath . $this->theme .  $_template)) {
            return $this->templatePath . $this->theme .  $_template;
        }

        throw new HttpException(200, '模板不存在:' . $this->theme . $_template);
    }
}
