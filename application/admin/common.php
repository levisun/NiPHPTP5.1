<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application\admin
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
    $_content = preg_replace('/( ){2,}/si', '', $_content);

    $cdn = request()->rootDomain() . request()->root() . '/';

    $meta = '</title>' . PHP_EOL .
            '<meta name="generator" content="NiPHP ' . NP_VERSION . '" />' . PHP_EOL .
            '<meta name="author" content="失眠小枕头 levisun.mail@gmail.com" />' . PHP_EOL .
            '<meta name="copyright" content="2013-' . date('Y') . ' NiPHP 失眠小枕头" />' . PHP_EOL .
            '<meta name="robots" content="none" />' . PHP_EOL .
            '<meta name="revisit-after" content="7 days" >' . PHP_EOL .
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
            '<link rel="dns-prefetch" href="//img.' . $cdn . '" />' . PHP_EOL .
            '<link href="//cdn.' . $cdn . 'favicon.ico" rel="shortcut icon" type="image/x-icon" />' . PHP_EOL;

    if (request()->isMobile()) {
        $meta .= '<meta name="apple-mobile-web-app-capable" content="yes" />' . PHP_EOL .
                 '<meta name="apple-mobile-web-app-status-bar-style" content="black" />' . PHP_EOL .
                 '<meta name="format-detection" content="telephone=yes" />' . PHP_EOL .
                 '<meta name="format-detection" content="email=yes" />' . PHP_EOL;
    }
    $_content = str_replace('</title>', $meta, $_content);

    $html = '<script type="text/javascript">' .
            'console.log("Copyright © 2013-' . date('Y') . ' http://www.NiPHP.com' .
            '\r\nAuthor 失眠小枕头 levisun.mail@gmail.com' .
            '\r\nCreate Date ' . date('Y-m-d H:i:s') .
            '\r\nRuntime ' . number_format(microtime(true) - app()->getBeginTime(), 6) . '秒' .
            '\r\nMemory ' . number_format((memory_get_usage() - app()->getBeginMem()) / 1048576, 2) . 'MB");' .
            '</script>' .
            '</body>';
    $_content = str_replace('</body>', $html, $_content);

    return $_content;
}

/**
 * 节点格式化
 * @param  array $_result
 * @param  int   $_pid
 * @return array
 */
function node_format($_result, $_pid = 0)
{
    $node = [];
    foreach ($_result as $key => $value) {
        if ($value['pid'] == $_pid) {
            $ext = '';
            for ($i=1; $i < $value['level']; $i++) {
                $ext .= '|__';
            }
            $value['title'] = $ext . $value['title'];

            $node[] = $value;

            $temp = node_format($_result, $value['id']);
            if (!empty($temp)) {
                $node = array_merge($node, $temp);
            }

            unset($_result[$key]);
        }
    }

    return $node;
}

/**
 * 删除旧的未保存的上传文件
 * @param  mixed $_path
 * @return void
 */
function remove_old_upload_file($_path = null)
{
    if (cookie('?__upfile')) {
        if (is_null($_path)) {
            // $_path=null时表示上传文件已写入库中，删除此记录
            cookie('__upfile', null);
        } else {
            // 删除上次上传没有保存文件
            $upfile = cookie('__upfile');
            if (!empty($upfile)) {
                \File::remove(env('root_path') . basename(request()->root()) . $upfile);
            }
            cookie('__upfile', $_path);
        }
    } else {
        cookie('__upfile', $_path);
    }
}

/**
 * 记录操作日志
 * @param  string $_msg
 * @param  string $_action
 * @return void
 */
function create_action_log($_msg, $_action = '')
{
    if (!$_action) {
        $_action = strtolower(request()->controller() . '_' . request()->action());
    }

    $map = [
        ['name', '=', $_action]
    ];
    $result = model('common/action')
    ->where($map)
    ->find();

    $data = [
        'action_id' => $result['id'],
        'user_id'   => session(config('user_auth_key')),
        'action_ip' => request()->ip(0, true),
        'module'    => request()->module(),
        'remark'    => $_msg,
    ];
    model('common/actionLog')
    ->added($data);

    // 删除过期日志
    $days = APP_DEBUG ? '-7 days' : '-30 days';
    $map = [
        ['create_time', '<=', strtotime($days)]
    ];
    model('common/actionLog')
    ->where($map)
    ->delete();
}
