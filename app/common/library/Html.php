<?php
/**
 *
 * HTML类 - 方法库
 *
 * @package   NiPHP
 * @category  app\common\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\library;

use think\exception\HttpException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\common\library\Filter;

class Html
{
    private $themeConfig;
    private $replace = [
        '__CSS__'    => '',
        '__IMG__'    => '',
        '__JS__'     => '',
        '__STATIC__' => '',
    ];

    public function __construct()
    {
        $cdn = '//cdn.' . Request::rootDomain() . Request::root() . '/theme/';

        $this->replace = [
            '__CSS__'    => $cdn . Request::app() . '/' . Config::get(Request::app() . '_theme', 'default') . '/css/',
            '__IMG__'    => $cdn . Request::app() . '/' . Config::get(Request::app() . '_theme', 'default') . '/img/',
            '__JS__'     => $cdn . Request::app() . '/' . Config::get(Request::app() . '_theme', 'default') . '/js/',
            '__STATIC__' => $cdn . 'static/',
        ];

        $path = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                'theme' . DIRECTORY_SEPARATOR .
                Request::app() . DIRECTORY_SEPARATOR .
                Config::get(Request::app() . '_theme', 'default') . DIRECTORY_SEPARATOR .
                'config.json';
        if (is_file($path)) {
            $this->themeConfig = file_get_contents($path);
            $this->themeConfig = Filter::default($this->themeConfig, true);
            $this->themeConfig = str_replace(
                array_keys($this->replace),
                array_values($this->replace),
                $this->themeConfig
            );
            $this->themeConfig = json_decode($this->themeConfig, true);
            if (!$this->themeConfig) {
                throw new HttpException(400, '模板配置文件错误[config.json]');
            }
        }

        unset($cdn, $path);
    }

    /**
     * 头部HTML
     * @access public
     * @param
     * @return string
     */
    public function meta(string $_title = null, string $_keywords = null, string $_description = null): string
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
        '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .
        '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' .
        '<meta http-equiv="Cache-Control" content="no-siteapp" />' .            // 禁止baidu转码
        '<meta http-equiv="Cache-Control" content="no-transform" />' .

        '<meta http-equiv="x-dns-prefetch-control" content="on" />' .

        '<link rel="dns-prefetch" href="//api.' . Request::rootDomain() . Request::root() . '" />' .
        '<link rel="dns-prefetch" href="//cdn.' . Request::rootDomain() . Request::root() . '" />' .

        '<link href="//cdn.' . Request::rootDomain() . Request::root() . '/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

        $meta .= $_title ? '<title>' . $_title . '</title>' : '';
        $meta .= $_keywords ? '<meta name="keywords" content="' . $_keywords . '" />' : '';
        $meta .= $_description ? '<meta name="description" content="' . $_description . '" />' : '';

        if (!empty($this->themeConfig['meta'])) {
            foreach ($this->themeConfig['meta'] as $m) {
                $meta .= '<meta ' . $m['type'] . ' ' . $m['content'] . ' />';
            }
        }

        if (!empty($this->themeConfig['css'])) {
            foreach ($this->themeConfig['css'] as $css) {
                $meta .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
            }
        }

        return $meta . '</head><body>';
    }

    /**
     * 底部HTML
     * @access public
     * @param
     * @return string
     */
    public function foot(): string
    {
        $foot = '<script type="text/javascript">' .
        'var request = {' .
            'domain:"' . Request::scheme() . '://' . Request::rootDomain() . Request::root() . '",' .
            'url:"' . url() . '",' .
            'param:' . json_encode(Request::param()) . ',' .
            'c:"' . Request::controller(true) . '",' .
            'a:"' . Request::action(true) . '",' .
            'api:{' .
                'query:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::app() . '/query.html",' .
                'handle:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::app() . '/handle.html",' .
                'upload:"//api.' . Request::rootDomain() . Request::root() . '/' . Request::app() . '/upload.html"' .
            '}' .
        '};' .
        '</script>';

        if (!empty($this->themeConfig['js'])) {
            foreach ($this->themeConfig['js'] as $js) {
                $foot .= '<script type="text/javascript" src="' . $js . '"></script>';
            }
        }

        // 插件加载


        $foot .= '<script type="text/javascript">' .
        'console.log("Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com' .
        '\r\nAuthor 失眠小枕头 levisun.mail@gmail.com' .
        '\r\nCreate Date ' . date('Y-m-d H:i:s') . '");' .
        '</script>';

        return $foot . '</body></html>';
    }
}
