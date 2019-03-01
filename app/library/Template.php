<?php
/**
 *
 * 服务层
 * 模板类
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\Response;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\library\Base64;
use app\library\Filter;
use app\library\Siteinfo;

class Template
{

    /**
     * 模板变量
     * @var array
     */
    protected $data = [];

    /**
     * 架构函数
     * @access public
     * @param  array $config
     */
    public function __construct()
    {
        $this->templatePath  = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
        $this->templatePath .= 'theme' . DIRECTORY_SEPARATOR;
        $this->templatePath .= Request::controller(true) . DIRECTORY_SEPARATOR;
        $this->templatePath .= Siteinfo::theme() . DIRECTORY_SEPARATOR;

        $this->templateReplace = [
            '{:__CSS__}'         => Config::get('cdn_host') . '/theme/' . Request::controller(true) . '/' . Siteinfo::theme() . '/css/',
            '{:__IMG__}'         => Config::get('cdn_host') . '/theme/' . Request::controller(true) . '/' . Siteinfo::theme() . '/img/',
            '{:__JS__}'          => Config::get('cdn_host') . '/theme/' . Request::controller(true) . '/' . Siteinfo::theme() . '/js/',
            '{:__STATIC__}'      => Config::get('cdn_host') . '/theme/static/',
            '{:__TITLE__}'       => Siteinfo::title(),
            '{:__KEYWORDS__}'    => Siteinfo::keywords(),
            '{:__DESCRIPTION__}' => Siteinfo::description(),
            '{:__BOTTOM_MSG__}'  => Siteinfo::bottom(),
            '{:__COPYRIGHT__}'   => Siteinfo::copyright(),
        ];

        $this->templateConfig = $this->parseTemplateConfig();
    }

    public function fetch(string $_template, array $_vars = [])
    {
        if (!$content = $this->templateBuildRead()) {
            $content = file_get_contents($this->parseTemplateFile($_template));
            $content = str_replace(array_keys($this->templateReplace), array_values($this->templateReplace), $content);

            if ($this->templateConfig['layout'] && strpos($content, '{:NOT_LAYOUT}') === false) {
                $content = str_replace('{:__CONTENT__}', $content, $this->parseTemplateLayout());
            }

            $content = $this->parseTemplateHead() . $content . $this->parseTemplateFoot();

            // 去除html空格与换行
            $replace = [
                '~>\s+<~'       => '><',
                '~>(\s+\n|\r)~' => '>',
            ];
            $content = preg_replace(array_keys($replace), array_values($replace), $content);

            // PHP代码安全
            $replace = [
                '/<\?php(.*?)\?>/si',
                '/<\?(.*?)\?>/si',
                '/<%(.*?)%>/si',
                '/<\?php|<\?|\?>|<%|%>/si',
            ];
            $content = preg_replace($replace, '', $content);

            $this->templateBuild($content);
        }

        $token = sha1(Request::header('USER-AGENT') . Request::ip() . Env::get('root_path') . strtotime(date('Ymd')));
        $token .= session_id() ? '.' . session_id() : '';
        $content = str_replace('{:__TOKEN__}', $token, $content);

        $content .= '<!-- ' . json_encode([
            $this->templateConfig['layout'] ? 'true' : 'false',
            Siteinfo::theme() . '/' . $_template,
            date('Y-m-d H:i:s')
        ]) . ' -->';

        $headers = [
            'Cache-Control' => 'max-age=3600,must-revalidate',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            'Expires'       => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT'
        ];
        if (!headers_sent() && extension_loaded('zlib') && strpos(Request::server('HTTP_ACCEPT_ENCODING'), 'gzip') !== false) {
            $content = gzencode($content, 4);
            $headers['Content-Encoding'] = 'gzip';
            $headers['Content-Length'] = strlen($content);
        }

        $response = Response::create($content)->header($headers);
        return $response->send();
        // throw new HttpResponseException($response);
    }

    public function templateBuildRead(): string
    {
        $path = Env::get('runtime_path') . 'html' . Base64::flag() . DIRECTORY_SEPARATOR;
        $path .= Request::subDomain() . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 777, true);
        }

        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('_', $url);
        $path .= $url ? $url . '.html' : 'index.html';

        if (APP_DEBUG === false && is_file($path) && filemtime($path) >= time() - rand(3600, 7200)) {
            return file_get_contents($path);
        } else {
            return '';
        }
    }

    public function templateBuild(string $_content)
    {
        $path = Env::get('runtime_path') . 'html' . Base64::flag() . DIRECTORY_SEPARATOR;
        $path .= Request::subDomain() . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 777, true);
        }

        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('_', $url);
        $path .= $url ? $url . '.html' : 'index.html';

        file_put_contents($path, $_content);
    }

    public function parseTemplateFoot(): string
    {
        list($root) = explode('.', Request::rootDomain(), 2);

        $foot = '<script type="text/javascript">' .
        'var NIPHP = {' .
            'domain:"' . '//' . Request::rootDomain() . Request::root() . '",' .
            'api:{' .
                'url:"' . Config::get('api_host') . '",'.
                'root:"' . $root . '",' .
                'version:"' . $this->templateConfig['version'] . '",' .
                'token:"{:__TOKEN__}"' .
            '},' .
            'cdn:{' .
                'css:"' . $this->templateReplace['{:__CSS__}'] . '",' .
                'img:"' . $this->templateReplace['{:__IMG__}'] . '",' .
                'js:"' . $this->templateReplace['{:__JS__}'] . '",' .
                'static:"' . $this->templateReplace['{:__STATIC__}'] . '",' .
            '},' .
            'url:"' . url() . '",' .
            'param:' . json_encode(Request::param()) .
        '};' .
        '</script>';
        unset($root);

        if (!empty($this->templateConfig['js'])) {
            foreach ($this->templateConfig['js'] as $js) {
                // $script = file_get_contents($js);
                // $script = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);
                // $foot .= '<script type="text/javascript" class="' . md5($js) . '">' . $script . '</script>';
                $foot .= '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }

        // 插件加载

        // 底部JS脚本
        $foot .= Siteinfo::script();

        return $foot . '</body></html>';
    }

    public function parseTemplateHead(): string
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
        '<link rel="dns-prefetch" href="' . Config::get('api_host') . '" />' .
        '<link rel="dns-prefetch" href="' . Config::get('cdn_host') . '" />' .

        '<link href="' . Config::get('cdn_host') . '/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

        // 网站标题 关键词 描述
        $head .= '<title>' . Siteinfo::title() . '</title>';
        $head .= '<meta name="keywords" content="' . Siteinfo::keywords() . '" />';
        $head .= '<meta name="description" content="' . Siteinfo::description() . '" />';

        if (!empty($this->templateConfig['meta'])) {
            foreach ($this->templateConfig['meta'] as $m) {
                $head .= '<meta ' . $m['type'] . ' ' . $m['content'] . ' />';
            }
        }
        // <meta name="apple-itunes-app" content="app-id=1191720421, app-argument=sspai://sspai.com">

        if (!empty($this->templateConfig['css'])) {
            foreach ($this->templateConfig['css'] as $css) {
                // $style = file_get_contents($css);
                // $style = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $style);
                // $style = preg_replace('/(\n|\r)/si', '', $style);
                // $style = preg_replace('/( ){2,}/si', '', $style);
                // $head .= '<style type="text/css" class="' . md5($css) . '">' . $style . '</style>';
                $head .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
            }
        }

        return $head . '</head><body>';
    }

    /**
     * 解析布局模板
     * @access private
     * @param  string $_template 模板名
     * @return string 模板路径
     */
    private function parseTemplateLayout(): string
    {
        if (is_file($this->templatePath . 'layout.html')) {
            $layout = file_get_contents($this->templatePath . 'layout.html');
            $layout = str_replace(array_keys($this->templateReplace), array_values($this->templateReplace), $layout);
            return $layout;
        }

        throw new HttpException(200, '布局模板不存在.');
    }

    /**
     * 解析模板配置
     * @access private
     * @param  string $_template 模板名
     * @return string 模板路径
     */
    private function parseTemplateConfig(): array
    {
        if (is_file($this->templatePath . 'config.json')) {
            $config = file_get_contents($this->templatePath . 'config.json');
            $config = Filter::default($config, true);
            $config = str_replace(array_keys($this->templateReplace), array_values($this->templateReplace), $config);
            $config = json_decode($config, true);
            if (!empty($config) && is_array($config)) {
                return $config;
            }
        }

        throw new HttpException(200, '模板配置文件错误.');
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
        if (is_file($this->templatePath . $_template)) {
            return $this->templatePath . $_template;
        }

        throw new HttpException(200, '模板不存在:', $_template);
    }
}