<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHP
 * @category  application\cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 自动添加HTML文档中的meta等信息
 * @param  string $_content
 * @return string
 */
function replace_meta($_content)
{
    $_content = preg_replace([
            '/<(\!DOCTYPE.*?)>(.*?)<(body.*?)>/si',
            '/<(\/body.*?)>(.*?)<(\/html.*?)>/si',
            '/( ){2,}/si',
        ], '', $_content);

    $siteinfo = logic(request()->module() . '/siteinfo')->query();
    $cdn = request()->rootDomain() . request()->root() . '/';
    $api = '//api.' . request()->rootDomain() . request()->root() . '/';
    $scheme = request()->scheme() . '://';

    $tpl_replace_string = config('template.tpl_replace_string');

    $head = '<!DOCTYPE html>' . PHP_EOL .
            '<html lang="en">' . PHP_EOL .
            '<head>' . PHP_EOL .
            '<meta charset="utf-8" />' . PHP_EOL .
            '<title>' . $siteinfo['title'] . '</title>' . PHP_EOL .
            '<meta name="generator" content="NiPHP ' . NP_VERSION . '" />' . PHP_EOL .
            '<meta name="author" content="失眠小枕头 levisun.mail@gmail.com" />' . PHP_EOL .
            '<meta name="copyright" content="2013-' . date('Y') . ' NiPHP 失眠小枕头" />' . PHP_EOL .
            '<meta name="robots" content="all" />' . PHP_EOL .
            '<meta name="revisit-after" content="7 days" />' . PHP_EOL .
            '<meta name="renderer" content="webkit" />' . PHP_EOL .
            '<meta name="force-rendering" content="webkit" />' . PHP_EOL .
            '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no" />' . PHP_EOL .
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL .
            '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />' . PHP_EOL .
            '<meta http-equiv="Cache-Control" content="no-siteapp" />' . PHP_EOL .
            // '<meta http-equiv="Widow-target" content="_top" />' . PHP_EOL .

            '<link rel="dns-prefetch" href="//cdn.' . $cdn . '" />' . PHP_EOL .
            '<link rel="dns-prefetch" href="//css.' . $cdn . '" />' . PHP_EOL .
            '<link rel="dns-prefetch" href="//js.' . $cdn . '" />' . PHP_EOL .
            '<link rel="dns-prefetch" href="//img.' . $cdn . '" />' . PHP_EOL .

            '<meta name="keywords" content="' . $siteinfo['website_keywords'] . '" />' . PHP_EOL .
            '<meta name="description" content="' . $siteinfo['website_description'] . '" />' . PHP_EOL .
            '<meta property="og:site_name" content="' . $siteinfo['website_name'] . '" />' . PHP_EOL .
            '<meta property="og:type" content="article" />' . PHP_EOL .
            '<meta property="og:title" content="' . $siteinfo['title'] . '" />' . PHP_EOL .
            '<meta property="og:url" content="' . request()->url(true) . '" />' . PHP_EOL .
            '<meta property="og:description" content="' . $siteinfo['website_description'] . '" />' . PHP_EOL .
            '<link href="' . $scheme . 'cdn.' . $cdn . 'favicon.ico" rel="shortcut icon" type="image/x-icon" />' . PHP_EOL;


    if (is_file(config('template.view_path') . 'config.json')) {
        $config = file_get_contents(config('template.view_path') . 'config.json');

        $config = str_replace(
            array_keys($tpl_replace_string),
            array_values($tpl_replace_string),
            $config
        );

        $config = json_decode($config, true);

        if (!empty($config['css'])) {
            foreach ($config['css'] as $css) {
                $head .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />' . PHP_EOL;
            }
        }

        if (!empty($config['js'])) {
            foreach ($config['js'] as $js) {
                $head .= '<script type="text/javascript" src="' . $js . '"></script>' . PHP_EOL;
            }
        }
    }

    $head .= '<script type="text/javascript">' .
             'var request = {' .
                 'domain: "' . $tpl_replace_string['__DOMAIN__'] . '",' .
                 'api: {' .
                    'query: "' . $api . 'cms/query.do"' .
                    // 'settle: "' . $tpl_replace_string['__API_SETTLE__'] . '",' .
                    // 'upload: "' . $tpl_replace_string['__API_UPLOAD__'] . '",' .
                    // 'getipinfo: "' . url('api/getipinfo', '', true) . '",' .
                 '},' .
                 'static: "' . $tpl_replace_string['__STATIC__'] . '",' .
                 'css: "' . $tpl_replace_string['__CSS__'] . '",' .
                 'js: "' . $tpl_replace_string['__JS__'] . '",' .
                 'img: "' . $tpl_replace_string['__IMG__'] . '"' .
             '};' .
             '</script>';

    $head .= '</head><body>' . PHP_EOL;

    $foot  = $siteinfo['script'];

    // 插件加载
    if (!empty($config['hook'])) {
        foreach ($config['hook'] as $hook) {
            $foot .= $hook;
        }
    }

    $foot .= '<script type="text/javascript">' .
             'console.log("Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com' .
             '\r\nAuthor 失眠小枕头 levisun.mail@gmail.com' .
             '\r\nCreate Date ' . date('Y-m-d H:i:s') .
             '\r\nRuntime ' . number_format(microtime(true) - app()->getBeginTime(), 6) . '秒' .
             '\r\nMemory ' . number_format((memory_get_usage() - app()->getBeginMem()) / 1048576, 2) . 'MB");' .
             '</script>' .  PHP_EOL .
             '</body>' . PHP_EOL . '</html>';

    return $head . $_content . $foot;
}
