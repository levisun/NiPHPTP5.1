<?php
/**
 *
 * 服务层
 * HTML类
 *
 * @package   NiPHP
 * @category  app\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server;

use think\App;
use think\Response;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Log;
use think\facade\Request;
use app\server\Accesslog;
use app\server\Base64;
use app\server\Filter;
use app\server\Siteinfo;

class Tpl
{
    private $themeConfig;
    private $replace;

    public function handle($event, App $app):void
    {
        // 减轻并发压力
        if (Request::isGet() && (new Accesslog)->isSpider() === false) {
            // 千分之一抛出异常
            if (rand(1, 1000) === 1) {
                Log::record('并发', 'alert');
                throw new HttpException(502);
            } else {
                $key = Request::server('HTTP_USER_AGENT') . Request::ip() . date('Y-m-d');
                $key = md5($key);

                if (Cache::has($key)) {
                    $intercept = Cache::get($key);
                } else {
                    $intercept = [
                        'expire'  => 60,
                        'runtime' => time(),
                        'total'   => 0,
                    ];
                }

                // 非法请求
                if ($intercept['total'] >= 50) {
                    Log::record('非法请求', 'alert');
                    throw new HttpException(502);
                }
                // 更新请求数
                elseif ($intercept['runtime'] + $intercept['expire'] >= time()) {
                    $intercept['total']++;
                }
                // 还原请求
                else {
                    $intercept = [
                        'expire'  => 60,
                        'runtime' => time(),
                        'total'   => 0,
                    ];
                }

                Cache::set($key, $intercept, 0);
            }
        }

        if (Request::isGet() && !in_array(Request::subDomain(), ['api', 'cdn'])) {
            $this->read();
        }
    }

    /**
     * 加载模板输出
     * @access public
     * @param  string $_template 模板文件名
     * @param  array  $_vars     模板输出变量
     * @return mixed
     */
    public function fetch(string $_template = '', array $_vars = []): void
    {
        $tpl_path = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                    'theme' . DIRECTORY_SEPARATOR .
                    Request::controller(true) . DIRECTORY_SEPARATOR .
                    Siteinfo::theme() . DIRECTORY_SEPARATOR;

        if (isWechat()) {
            $tpl_path .= 'wechat' . DIRECTORY_SEPARATOR;
        } elseif (Request::isMobile()) {
            $tpl_path .= 'mobile' . DIRECTORY_SEPARATOR;
        }

        $_template = $_template ? $_template . '.html' : Request::action(true) . '.html';

        if (!is_file($tpl_path . $_template)) {
            throw new HttpException(200, '模板文件未找到!' . Request::controller(true) . DIRECTORY_SEPARATOR . Siteinfo::theme() . DIRECTORY_SEPARATOR . $_template);
        }

        $cdn = '//cdn.' . Request::rootDomain() . Request::root() . '/theme/' .
               Request::controller(true) . '/' . Siteinfo::theme() . '/';

        $this->replace = [
            '__CSS__'         => $cdn . 'css/',
            '__IMG__'         => $cdn . 'img/',
            '__JS__'          => $cdn . 'js/',
            '__STATIC__'      => '//cdn.' . Request::rootDomain() . Request::root() . '/theme/static/',
            '__TITLE__'       => Siteinfo::title(),
            '__KEYWORDS__'    => Siteinfo::keywords(),
            '__DESCRIPTION__' => Siteinfo::description(),
            '__BOTTOM_MSG__'  => Siteinfo::bottom(),
            '__COPYRIGHT__'   => Siteinfo::copyright(),
            '__:CONTROLLER__' => $cdn . 'js/' . Request::controller(true),
            '__:ACTION__'     => $cdn . 'js/' . Request::action(true),
        ];

        // 模板配置
        if (is_file($tpl_path . 'config.json')) {
            $this->themeConfig = file_get_contents($tpl_path . 'config.json');
            $this->themeConfig = Filter::default($this->themeConfig, true);
            $this->themeConfig = str_replace(
                array_keys($this->replace),
                array_values($this->replace),
                $this->themeConfig
            );
            $this->themeConfig = json_decode($this->themeConfig, true);
            if (!$this->themeConfig) {
                throw new HttpException(200, '模板配置文件错误[config.json]');
            }
            if (!isset($this->themeConfig['version'])) {
                $this->themeConfig['version'] = date('YmdHis', filemtime($tpl_path . 'config.json'));
            }
        }

        // 布局模板
        if (!empty($this->themeConfig['layout']) && $this->themeConfig['layout'] && is_file($tpl_path . 'layout.html')) {
            $content = file_get_contents($tpl_path . 'layout.html');
            $content = str_replace('__CONTENT__', file_get_contents($tpl_path . $_template), $content);
        } else {
            $content = file_get_contents($tpl_path . $_template);
        }

        // 替换字符
        $content = str_replace(array_keys($this->replace), array_values($this->replace), $content);
        // 去除html空格与换行
        $replace    = [
            '~>\s+<~'       => '><',
            '~>(\s+\n|\r)~' => '>',
            // '~font-size:(.*?);~' => '',

            // '~//[ a-zA-Z\u4e00-\u9fa5^\x00-\xff]?(\n|\r)+~' => '',
        ];
        $content = preg_replace(array_keys($replace), array_values($replace), $content);

        // PHP代码安全
        $content = preg_replace([
            '/<\?php(.*?)\?>/si',
            '/<\?(.*?)\?>/si',
            '/<%(.*?)%>/si',
            '/<\?php|<\?|\?>|<%|%>/si',
        ], '', $content);


        $content = $this->meta() . $content . $this->foot();

        // 添加HTML生成记录
        $content .= '<!-- ' . json_encode([
            'layout'   => $this->themeConfig['layout'] ? 'true' : 'false',
            'template' => Siteinfo::theme() . '/' . $_template,
            'date'     => date('Y-m-d H:i:s')
        ]) . ' -->';

        $this->build($content);

        $response = Response::create($content);
        throw new HttpResponseException($response);
    }

    /**
     * 头部HTML
     * @access protected
     * @param
     * @return string
     */
    protected function meta(): string
    {
        $meta = '<!DOCTYPE html>' .
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

        '<meta http-equiv="x-dns-prefetch-control" content="on" />' .

        '<link rel="dns-prefetch" href="//api.' . Request::rootDomain() . Request::root() . '" />' .
        '<link rel="dns-prefetch" href="//cdn.' . Request::rootDomain() . Request::root() . '" />' .

        '<link href="//cdn.' . Request::rootDomain() . Request::root() . '/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

        // 网站标题 关键词 描述
        $meta .= '<title>' . Siteinfo::title() . '</title>';
        $meta .= '<meta name="keywords" content="' . Siteinfo::keywords() . '" />';
        $meta .= '<meta name="description" content="' . Siteinfo::description() . '" />';

        if (!empty($this->themeConfig['meta'])) {
            foreach ($this->themeConfig['meta'] as $m) {
                $meta .= '<meta ' . $m['type'] . ' ' . $m['content'] . ' />';
            }
        }
        // <meta name="apple-itunes-app" content="app-id=1191720421, app-argument=sspai://sspai.com">

        if (!empty($this->themeConfig['css'])) {
            foreach ($this->themeConfig['css'] as $css) {
                $meta .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
            }
        }

        return $meta . '</head><body>';
    }

    /**
     * 底部HTML
     * @access protected
     * @param
     * @return string
     */
    protected function foot(): string
    {
        list($root) = explode('.', Request::rootDomain(), 2);
        $token = sha1(Request::header('USER-AGENT') . Request::ip() . Env::get('root_path') . strtotime(date('Ymd')));
        $token .= session_id() ? '.' . session_id() : '';
        $foot = '<script type="text/javascript">' .
        'var NIPHP = {' .
            'domain:"' . '//' . Request::rootDomain() . Request::root() . '",' .
            'api:{' .
                'url:"//api.' . Request::rootDomain() . Request::root() . '",'.
                'root:"' . $root . '",' .
                'version:"' . $this->themeConfig['version'] . '",' .
                'token:"' . $token . '"' .
            '},'.
            'url:"' . url() . '",' .
            'param:' . json_encode(Request::param()) . ',' .
            'c:"' . Request::controller(true) . '",' .
            'a:"' . Request::action(true) . '"' .
        '};' .
        '</script>';
        unset($root, $token);

        if (!empty($this->themeConfig['js'])) {
            foreach ($this->themeConfig['js'] as $js) {
                $foot .= '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }

        // 插件加载

        // 底部JS脚本
        $foot .= Siteinfo::script();

        // 附加信息
        $foot .= '<script type="text/javascript">' .
        'console.log("Powered by NiPHP");' .
        'console.log("失眠小枕头 levisun.mail@gmail.com");' .
        'console.log("Copyright © 2013-' . date('Y') .'");' .
        '</script>';

        return $foot . '</body></html>';
    }

    /**
     * 创建静态文件
     * @access protected
     * @param
     * @return void
     */
    protected function build(string $_data): void
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

        if (function_exists('gzcompress')) {
            $_data = gzcompress($_data, 3);
        }

        file_put_contents($path, $_data);
    }

    /**
     * 读取静态文件
     * @access protected
     * @param
     * @return void
     */
    protected function read()
    {
        $path = Env::get('runtime_path') . 'html' . Base64::flag() . DIRECTORY_SEPARATOR;
        $path .= Request::subDomain() . DIRECTORY_SEPARATOR;

        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('_', $url);
        $path .= $url ? $url . '.html' : 'index.html';

        // 模板存在并在缓存期内
        // 读取缓存
        if (APP_DEBUG === false && is_file($path) && filemtime($path) >= time() - rand(1140, 3000)) {
            $headers = [
                'Cache-Control' => 'max-age=1140,must-revalidate',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'Expires'       => gmdate('D, d M Y H:i:s', time() + 1140) . ' GMT'
            ];

            $content = file_get_contents($path);
            if (function_exists('gzcompress')) {
                $content = gzuncompress($content);
            }
            $response = Response::create($content)->header($headers);
            throw new HttpResponseException($response);
        }
    }
}
