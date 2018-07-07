<?php
/**
 *
 * 应用公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

use think\facade\Debug;
use think\facade\Lang;
use think\Request;

function rl()
{
    $res = get_browser(null, true);

    // mb_internal_encoding();
    print_r($res);
}

/**
 * 随机码  邀请码  兑换码
 * @param
 * @return string
 */
function random_code()
{
    $code = sprintf('%x', crc32(microtime()));
    if (strlen($code) < 8) {
        return random_code();
    } else {
        return $code;
    }
}

/**
 * 文件大小
 * @param  string $_size_or_path 文件大小或文件路径
 * @return string
 */
function file_size($_size_or_path)
{
    if (strpos($_size_or_path, '.') !== false && is_file($_size_or_path)) {
        $_size_or_path = filesize($_size_or_path);
    }

    $unit = ['B', 'KB', 'MB', 'GB', 'TB'];

    $pos = 0;
    while ($_size_or_path >= 1024) {
        $_size_or_path /= 1024;
        $pos++;
    }

    return round($_size_or_path, 2) . ' ' . $unit[$pos];
}

/**
 * 缓解并发压力
 * @param
 * @return void
 */
function concurrent_error()
{
    if (request()->isGet() && rand(0, 999) === 0) {
        abort(500, '并发压力');
    }
}

/**
 * 是否微信请求
 * @param
 * @return boolean
 */
function is_wechat_request()
{
    $agent = request()->header('user-agent');
    return strpos($agent, 'MicroMessenger') !== false ? true : false;
}

/**
 * 模板过滤
 * @param  string $_content
 * @return string
 */
function view_filter($_content)
{
    $pattern = [
        '/<\!--.*?-->/si'                 => '',    // HTML注释
        '/(\/\*).*?(\*\/)/si'             => '',    // JS注释
        '/(\r|\n| )+(\/\/).*?(\r|\n)+/si' => '',    // JS注释
        '/( ){2,}/si'                     => '',    // 空格
        '/(\r|\n|\f)/si'                  => '',    // 回车
    ];

    $_content = preg_replace(array_keys($pattern), array_values($pattern), $_content);
    Hook::exec(['app\\common\\behavior\\HtmlCacheBehavior', 'write'], $_content);

    return $_content;
}

/**
 * 实例化模型
 * @param  string $_name  [模块名/][业务名/]控制器名
 * @return object
 */
function logic($_name)
{
    if (strpos($_name, '/') !== false) {
        $count = count(explode('/', $_name));
        if ($count == 3) {
            list($module, $layer, $_name) = explode('/', $_name, 3);
            if ($layer !== 'logic') {
                $layer = 'logic\\' . $layer;
            }
        } elseif ($count == 2) {
            list($module, $_name) = explode('/', $_name, 2);
            $layer = 'logic';
        } else {
            $module = request()->module();
            $layer = 'logic';
        }
    }

    return app()->controller($module . '/' . $_name, $layer, false);
}

/**
 * 实例化模型
 * @param  string $_name [模块名/]模型名
 * @return object
 */
function model($_name = '')
{
    if (strpos($_name, '/') !== false) {
        // 支持模块
        list($module, $_name) = explode('/', $_name, 2);
    } else {
        $module = request()->module();
    }

    return app()->model($_name, 'model', false, $module);
}

/**
 * 实例化验证器
 * @param  string $_name  [模块名/][业务名/]验证器名[.场景]
 * @param  array  $_data  验证数据
 * @return mixed
 */
function validate($_name, $_data)
{
    if (strpos($_name, '/') !== false) {
        $count = count(explode('/', $_name));
        if ($count == 3) {
            list($module, $layer, $_name) = explode('/', $_name, 3);
            if ($layer !== 'validate') {
                $layer = 'validate\\' . $layer;
            }
        } elseif ($count == 2) {
            list($module, $_name) = explode('/', $_name, 2);
            $layer = 'validate';
        } else {
            $module = request()->module();
            $layer = 'validate';
        }
    }

    // 支持场景
    if (strpos($_name, '.') !== false) {
        list($_name, $scene) = explode('.', $_name, 2);
    }

    $v = app()->validate($_name, $layer, false, $module);
    if (!empty($scene)) {
        $v->scene($scene);
    }

    if (!$v->check($_data)) {
        $return = $v->getError();
    } else {
        $return = true;
    }

    return $return;
}

/**
 * 获取语言变量值
 * @param  string $_name 语言变量名
 * @param  array  $_vars 动态变量值
 * @param  string $_lang 语言
 * @return mixed
 */
function lang($_name, $_vars = [], $_lang = '')
{
    if ($_name == ':load') {
        // 允许的语言
        Lang::setAllowLangList(config('lang_list'));

        $lang_path  = env('app_path') . request()->module();
        $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
        $lang_path .= Lang::detect() . DIRECTORY_SEPARATOR;

        // 加载全局语言包
        Lang::load($lang_path . Lang::detect() . '.php');

        // 加载对应语言包
        $lang_name  = strtolower(request()->controller()) . DIRECTORY_SEPARATOR;
        $lang_name .= strtolower(request()->action());
        Lang::load($lang_path . $lang_name . '.php');
        $return = true;
    } elseif ($_name == ':detect') {
        $return = Lang::detect();
    } else {
        $return = Lang::get($_name, $_vars, $_lang);
    }
    return $return;
}

/**
 * 密码加密
 * @param  string $_password
 * @param  string $_salt
 * @return string
 */
function md5_password($_password, $_salt)
{
    $_password = md5(trim($_password));
    $_password = md5($_password . $_salt);
    return $_password;
}

/**
 * 运行时间与占用内存
 * @param  boolean $_start
 * @return mixed
 */
use_time_memory(true);
function use_time_memory($_start = false)
{
    if (!APP_DEBUG) {
        return ;
    }

    if ($_start) {
        Debug::remark('memory_start');
    } else {
        return
        lang('run time') .
        Debug::getRangeTime('memory_start', 'end', 4) . ' S/' .
        lang('run memory') .
        Debug::getMemPeak('memory_start', 'end', 4);

        /* . ' ' .
        lang('run file load') .
        count(get_included_files())*/
    }
}

/**
 * 清除运行垃圾文件
 * @param
 * @return void
 */
remove_rundata();
function remove_rundata()
{
    if (APP_DEBUG === false && rand(0, 29) !== 0) {
        return false;
    }

    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;

    $all_files = [];
    $files = [
        'cache' => (array) glob($dir . 'cache' . DIRECTORY_SEPARATOR . '*'),
        'log'   => (array) glob($dir . 'log' . DIRECTORY_SEPARATOR . '*'),
        'html'  => (array) glob($dir . 'html' . DIRECTORY_SEPARATOR . '*'),
        'temp'  => (array) glob($dir . 'temp' .  DIRECTORY_SEPARATOR . '*'),
        // 'backup' => (array) glob($dir . 'public' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . '*'),
    ];

    $child = [];
    foreach ($files as $key => $dir_name) {
        if ($key !== 'temp') {
            $child = [];
            foreach ($dir_name as $path) {
                $arr = (array) glob($path . DIRECTORY_SEPARATOR . '*');
                if ($arr) {
                    $child = array_merge($child, $arr);
                } else {
                    $child[] = $path;
                }
            }
            $all_files = array_merge($all_files, $child);

            // 目录中没有文件时将目录加入到待清理数据中
            if (empty($child)) {
                $all_files = array_merge($all_files, $dir_name);
            }
        } else {
            $all_files = array_merge($all_files, $dir_name);
        }
    }

    shuffle($all_files);
    $all_files = array_slice($all_files, 0, 100);

    $days = APP_DEBUG ? strtotime('-4 hour') : strtotime('-90 days');
    foreach ($all_files as $path) {
        if (is_file($path)) {
            if (filectime($path) <= $days) {
                @unlink($path);
            }
        } elseif (is_dir($path)) {
            @rmdir($path);
        }
    }
}

/**
 * 字符串加密
 * @param  string $_str     加密前的字符串
 * @param  string $_authkey 密钥
 * @return string           加密后的字符串
 */
function encrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $coded = '';
    $keylength = strlen($_authkey);
    for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
        $coded .= substr($_str, $i, $keylength) ^ $_authkey;
    }
    return str_replace('=', '', base64_encode($coded));
}

/**
 * 字符串解密
 * @param  string $_str     加密后的字符串
 * @param  string $_authkey 密钥
 * @return string           加密前的字符串
 */
function decrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $coded = '';
    $keylength = strlen($_authkey);
    $_str = base64_decode($_str);
    for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
        $coded .= substr($_str, $i, $keylength) ^ $_authkey;
    }
    return $coded;
}

/**
 * 过滤XSS
 * @param  string $_data
 * @return string
 */
function escape_xss($_data)
{
    if (is_array($_data)) {
        foreach ($_data as $key => $value) {
            $_data[$key] = escape_xss($value);
        }
    } else {
        $pattern = [
            '/<\?php(.*?)\?>/si'                            => '',
            '/<\?(.*?)\?>/si'                               => '',
            '/<%(.*?)%>/si'                                 => '',
            '/<\?php|<\?|\?>|<%|%>/si'                      => '',

            '/on([a-zA-Z]*?)(=)["|\'](.*?)["|\']/si'        => '',
            '/(javascript:)(.*?)(\))/si'                    => '',
            '/<\!--.*?-->/si'                               => '',
            '/<(\!.*?)>/si'                                 => '',

            '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si' => '',
            '/<(\/?javascript.*?)>/si'                      => '',

            '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si'     => '',
            '/<(\/?vbscript.*?)>/si'                        => '',

            '/<(expression.*?)>(.*?)<(\/expression.*?)>/si' => '',
            '/<(\/?expression.*?)>/si'                      => '',

            '/<(applet.*?)>(.*?)<(\/applet.*?)>/si'         => '',
            '/<(\/?applet.*?)>/si'                          => '',

            '/<(xml.*?)>(.*?)<(\/xml.*?)>/si'               => '',
            '/<(\/?xml.*?)>/si'                             => '',

            '/<(blink.*?)>(.*?)<(\/blink.*?)>/si'           => '',
            '/<(\/?blink.*?)>/si'                           => '',

            '/<(link.*?)>(.*?)<(\/link.*?)>/si'             => '',
            '/<(\/?link.*?)>/si'                            => '',

            '/<(script.*?)>(.*?)<(\/script.*?)>/si'         => '',
            '/<(\/?script.*?)>/si'                          => '',

            '/<(embed.*?)>(.*?)<(\/embed.*?)>/si'           => '',
            '/<(\/?embed.*?)>/si'                           => '',

            '/<(object.*?)>(.*?)<(\/object.*?)>/si'         => '',
            '/<(\/?object.*?)>/si'                          => '',

            '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si'         => '',
            '/<(\/?iframe.*?)>/si'                          => '',

            '/<(frame.*?)>(.*?)<(\/frame.*?)>/si'           => '',
            '/<(\/?frame.*?)>/si'                           => '',

            '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si'     => '',
            '/<(\/?frameset.*?)>/si'                        => '',

            '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si'         => '',
            '/<(\/?ilayer.*?)>/si'                          => '',

            '/<(layer.*?)>(.*?)<(\/layer.*?)>/si'           => '',
            '/<(\/?layer.*?)>/si'                           => '',

            '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si'       => '',
            '/<(\/?bgsound.*?)>/si'                         => '',

            '/<(title.*?)>(.*?)<(\/title.*?)>/si'           => '',
            '/<(\/?title.*?)>/si'                           => '',

            '/<(base.*?)>(.*?)<(\/base.*?)>/si'             => '',
            '/<(\/?base.*?)>/si'                            => '',

            '/<(meta.*?)>(.*?)<(\/meta.*?)>/si'             => '',
            '/<(\/?meta.*?)>/si'                            => '',

            '/<(style.*?)>(.*?)<(\/style.*?)>/si'           => '',
            '/<(\/?style.*?)>/si'                           => '',

            '/<(html.*?)>(.*?)<(\/html.*?)>/si'             => '',
            '/<(\/?html.*?)>/si'                            => '',

            '/<(head.*?)>(.*?)<(\/head.*?)>/si'             => '',
            '/<(\/?head.*?)>/si'                            => '',

            '/<(body.*?)>(.*?)<(\/body.*?)>/si'             => '',
            '/<(\/?body.*?)>/si'                            => '',

            // 多余回车
            '/[\r\n\f]+</si'     => '<',
            '/>[\r\n\f]+/si'     => '>',

            // SQL关键字
            '/(and )/si'     => '&#97;nd ',
            '/(between)/si'  => '&#98;etween',
            '/(chr)/si'      => '&#99;hr',
            '/(char)/si'     => '&#99;har',
            '/(count )/si'   => '&#99;ount ',
            '/(create)/si'   => '&#99;reate',
            '/(declare)/si'  => '&#100;eclare',
            '/(delete)/si'   => '&#100;elete',
            '/(execute)/si'  => '&#101;xecute',
            '/(insert)/si'   => '&#105;nsert',
            '/(join)/si'     => '&#106;oin',
            '/(update)/si'   => '&#117;pdate',
            '/(master)/si'   => '&#109;aster',
            '/(mid )/si'     => '&#109;id ',
            '/(or )/si'      => '&#111;r ',
            '/(select)/si'   => '&#115;elect',
            '/(truncate)/si' => '&#116;runcate',
            '/(where)/si'    => '&#119;here',
        ];
        $_data = preg_replace(array_keys($pattern), array_values($pattern), $_data);

        $pattern = [
            // 全角转半角
            '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5',
            '６' => '6', '７' => '7', '８' => '8', '９' => '9',

            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F',
            'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L',
            'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R',
            'Ｓ' => 'S', 'Ｔ' => 'T', 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X',
            'Ｙ' => 'Y', 'Ｚ' => 'Z',

            'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f',
            'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l',
            'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r',
            'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',


            '〔' => '[', '【' => '[', '〖' => '[',
            '〕' => ']', '】' => ']', '〗' => ']',
            '＋' => '&#43;',
            '！' => '&#33;',
            '｜' => '&#124;',
            '￥' => '&yen;',
            '〃' => '&quot;',
            '＂' => '&quot;',
            '－' => '&ndash;',
            '～' => '&#126;',
            '…' => '&#133;',
            '（' => '&#40;',
            '）' => '&#41;',
            '｛' => '&#123;',
            '｝' => '&#125;',
            '？' => '&#129;',
            '％' => '&#37;',
            '：' => '&#58;',

            '　' => '',

            // 特殊字符
            '‖' => '&#124;',
            '”' => '&rdquo;',
            '“' => '&ldquo;',
            '’' => '&rsquo;',
            '‘' => '&lsquo;',
            '™' => '&trade;',
            '®' => '&reg;',
            '©' => '&copy;',
            '—' => '&ndash;',
            '×' => '&times;',
            '÷' => '&divide;',
            '℃' => '&#8451;',
            '℉' => '&#8457;',

            // 安全字符
            '|'  => '&#124;',
            '*'  => '&#42;',
            '`'  => '&acute;',
            '\\' => '&#92;',
            '~'  => '&#126;',
            '‚'  => '&sbquo;',
            // ','  => '&#44;',
            // '.'  => '&#46;',
            '^'  => '&#94;',

            // HTML中的JS无法执行
            // '\'' => '&#039;',
            // '%'  => '&#37;',
            // '!'  => '&#33;',
            // '@'  => '&#64;',
            // '-'  => '&ndash;',
            // '?'  => '&#129;',
            // '+'  => '&#43;',
            // ':'  => '&#58;',
            // '='  => '&#61;',
            // '('  => '&#40;',
            // ')'  => '&#41;',
        ];

        $_data = str_replace(array_keys($pattern), array_values($pattern), $_data);

        // 过虑emoji表情 替换成*
        // $value = json_encode($_data);
        // $value = preg_replace('/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/', '&#42;', $value);
        // $_data = json_decode($value);

        // 个性字符过虑
        // $rule = '/[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s\_\-\(\)\[\]\{\}\|\?\/\!\@\#\$\%\^\&\+\=\:\;\'\"\<\>\,\.\，\。\《\》\\\\]+/u';
        // $_data = preg_replace($rule, '', $_data);
    }

    return $_data;
}
