<?php
/**
 *
 * HTML类 - 方法库
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
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Log;
use think\facade\Request;
use app\server\Base64;
use app\server\Filter;
use app\server\Garbage;
use app\server\Siteinfo;
use app\server\Accesslog;

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

        if (Request::isGet() && APP_DEBUG === false) {
            $this->redirect();
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
            throw new HttpException(200, '模板文件未找到!' . $_template);
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
            '__COPYRIGHT__'   => Siteinfo::copyright()
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


        $html = $this->meta() . $content . $this->foot();

        // 添加HTML生成记录
        $html .= '<!-- ' . json_encode([
            'url'      => url(),
            'layout'   => $this->themeConfig['layout'] ? 'true' : 'false',
            'template' => Siteinfo::theme() . '/' . $_template,
            'date'     => date('Y-m-d H:i:s'),
            'static'   => APP_DEBUG ? 'false' : 'true',
            ''
        ]) . ' -->';

        Response::create($html)
        ->allowCache(true)
        ->send();

        $this->build($html);

        unset($tpl_path, $cdn, $content, $html);
        die();
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
        $foot = '<script type="text/javascript">' .
        'var request = {' .
            'domain:"' . Request::scheme() . '://' . Request::rootDomain() . Request::root() . '",' .
            'url:"' . url() . '",' .
            'param:' . json_encode(Request::param()) . ',' .
            'c:"' . Request::controller(true) . '",' .
            'a:"' . Request::action(true) . '",' .
            'api:{' .
                'query:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::controller(true) . '/query.html",' .
                'handle:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::controller(true) . '/handle.html",' .
                'upload:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::controller(true) . '/upload.html"' .
            '}' .
        '};' .
        '</script>';

        if (!empty($this->themeConfig['js'])) {
            foreach ($this->themeConfig['js'] as $js) {
                $foot .= '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }

        // 插件加载

        // 底部JS脚本
        $script = Siteinfo::script();
        $foot .= $script ? $script : '';

        // 附加信息
        $foot .= '<script type="text/javascript">' .
        'console.log("Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com' .
        '\r\nAuthor 失眠小枕头 levisun.mail@gmail.com' .
        '\r\nCreate Date ' . date('Y-m-d H:i:s') . '");' .
        '</script>';

        return $foot . '</body></html>';
    }

    /**
     * 创建静态文件
     * @access protected
     * @param
     * @return string
     */
    protected function build(string $_data): void
    {
        $path = Env::get('runtime_path') . 'html_' . Base64::flag() . DIRECTORY_SEPARATOR;
        $path .= Request::subDomain() . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 777, true);
        }
        $url = Request::path();
        $url = explode('/', $url);
        $url = array_unique($url);
        $url = implode('_', $url);
        $path .= $url ? $url . '.html' : 'index.html';

        file_put_contents($path, $_data);
    }

    /**
     * [redirect description]
     * @return [type] [description]
     */
    public function redirect()
    {
        $path = Env::get('runtime_path') . 'html_' . Base64::flag() . DIRECTORY_SEPARATOR;
        $path .= Request::subDomain() . DIRECTORY_SEPARATOR;

        $url = explode('/', Request::path());
        $url = array_unique($url);
        $url = implode('_', $url);
        $path .= $url ? $url . '.html' : 'index.html';

        if (is_file($path) && filemtime($path) >= strtotime('-' . rand(20, 120) . ' minute')) {
            (new Accesslog)->record();
            (new Garbage)->remove();
            Response::create(file_get_contents($path))
            ->allowCache(true)
            ->send();
            die();
        }
    }
}
