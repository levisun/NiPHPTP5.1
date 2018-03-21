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

/**
 * 实例化模型
 * @param  string $_name  [模块名/]控制器名
 * @param  string $_layer 业务层名
 * @return object
 */
function logic($_name, $_layer = 'logic')
{
    if (strpos($_name, '/') === false) {
        $_name = request()->module() . '/' . $_name;
    }

    if ($_layer !== 'logic') {
        // 支持业务
        $_layer = 'logic\\' . $_layer;
    }

    return app()->controller($_name, $_layer, false);
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
        list($module, $_name) = explode('/', $_name);
    } else {
        $module = request()->module();
    }

    return app()->model($_name, 'model', false, $module);
}

/**
 * 实例化验证器
 * @param  string $_name  [模块名/]验证器名[.场景]
 * @param  array  $_data  验证数据
 * @param  string $_layer 业务层名
 * @return mixed
 */
function validate($_name, $_data, $_layer = 'validate')
{
    if (strpos($_name, '/') !== false) {
        // 支持模块
        list($module, $_name) = explode('/', $_name);
    } else {
        $module = request()->module();
    }

    if ($_layer !== 'validate') {
        // 支持业务
        $_layer = 'validate\\' . $_layer;
    }

    if (strpos($_name, '.') !== false) {
        // 支持场景
        list($_name, $scene) = explode('.', $_name);
    }

    $v = app()->validate($_name, $_layer, false, $module);
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
 * 运行时间与占用内存
 * @param  boolean $_start
 * @return mixed
 */
use_time_memory(true);
function use_time_memory($_start = false)
{
    if ($_start) {
        Debug::remark('memory_start');
    } else {
        return
        Debug::getRangeTime('memory_start', 'end', 4) . ' S/' .
        Debug::getMemPeak('memory_start', 'end', 4);

        /* . ' ' .
        lang('run file load') .
        count(get_included_files())*/
    }
}

/**
 * 清除运行垃圾文件
 * 开发模式不自动清除
 * @param
 * @return void
 */
function remove_rundata()
{
    if (rand(0, 29) !== 0) {
        return false;
    }

    $dir  = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    $all_files = [];
    $files = [
        'cache' => (array) glob($dir . 'runtime' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . '*'),
        'log'   => (array) glob($dir . 'runtime' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . '*'),
        'tmep'  => (array) glob($dir . 'runtime' . DIRECTORY_SEPARATOR . 'temp' .  DIRECTORY_SEPARATOR . '*'),
        // 'backup' => (array) glob($dir . 'public' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . '*'),
    ];

    $child = [];
    foreach ($files as $dir_name) {
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
    }

    shuffle($all_files);
    $all_files = array_slice($all_files, 0, 1000);

    $days = APP_DEBUG ? '-7 days' : '-30 days';
    foreach ($all_files as $path) {
        if (filectime($path) <= strtotime($days)) {
            if (is_dir($path)) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
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
            '/<\?php(.*?)\?>/si',
            '/<\?(.*?)\?>/si',
            '/<%(.*?)%>/si',
            '/<\?php|<\?|\?>|<%|%>/si',

            '/on([a-zA-Z]*?)(=)["|\'](.*?)["|\']/si',
            '/(javascript:)(.*?)(\))/si',
            '/<\!--.*?-->/si',
            '/<(\!.*?)>/si',

            '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si',
            '/<(\/?javascript.*?)>/si',

            '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',
            '/<(\/?vbscript.*?)>/si',

            '/<(expression.*?)>(.*?)<(\/expression.*?)>/si',
            '/<(\/?expression.*?)>/si',

            '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',
            '/<(\/?applet.*?)>/si',

            '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',
            '/<(\/?xml.*?)>/si',

            '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',
            '/<(\/?blink.*?)>/si',

            '/<(link.*?)>(.*?)<(\/link.*?)>/si',
            '/<(\/?link.*?)>/si',

            '/<(script.*?)>(.*?)<(\/script.*?)>/si',
            '/<(\/?script.*?)>/si',

            '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',
            '/<(\/?embed.*?)>/si',

            '/<(object.*?)>(.*?)<(\/object.*?)>/si',
            '/<(\/?object.*?)>/si',

            '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',
            '/<(\/?iframe.*?)>/si',

            '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',
            '/<(\/?frame.*?)>/si',

            '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si',
            '/<(\/?frameset.*?)>/si',

            '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',
            '/<(\/?ilayer.*?)>/si',

            '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',
            '/<(\/?layer.*?)>/si',

            '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',
            '/<(\/?bgsound.*?)>/si',

            '/<(title.*?)>(.*?)<(\/title.*?)>/si',
            '/<(\/?title.*?)>/si',

            '/<(base.*?)>(.*?)<(\/base.*?)>/si',
            '/<(\/?base.*?)>/si',

            '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',
            '/<(\/?meta.*?)>/si',

            '/<(style.*?)>(.*?)<(\/style.*?)>/si',
            '/<(\/?style.*?)>/si',

            '/<(html.*?)>(.*?)<(\/html.*?)>/si',
            '/<(\/?html.*?)>/si',

            '/<(head.*?)>(.*?)<(\/head.*?)>/si',
            '/<(\/?head.*?)>/si',

            '/<(body.*?)>(.*?)<(\/body.*?)>/si',
            '/<(\/?body.*?)>/si',
        ];

        $_data = preg_replace($pattern, '', $_data);

        $pattern = [
            '/[  ]+/si'  => ' ',    // 多余空格
            '/[\s]+</si' => '<',    // 多余回车
            '/>[\s]+/si' => '>',

            // SQL关键字
            '/( and)/si'     => ' &#97;nd',
            '/(between)/si'  => '&#98;etween',
            '/( chr)/si'     => ' &#99;hr',
            '/( char)/si'    => ' &#99;har',
            '/( count)/si'   => ' &#99;ount',
            '/(create)/si'   => '&#99;reate',
            '/(declare)/si'  => '&#100;eclare',
            '/(delete)/si'   => '&#100;elete',
            '/(execute)/si'  => '&#101;xecute',
            '/(insert)/si'   => '&#105;nsert',
            '/(join)/si'     => '&#106;oin',
            '/(update)/si'   => '&#117;pdate',
            '/(master)/si'   => '&#109;aster',
            '/( mid)/si'     => ' &#109;id',
            '/( or)/si'      => ' &#111;r',
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

            '（' => '(', '）' => ')',
            '〔' => '[', '【' => '[', '〖' => '[',
            '〕' => ']', '】' => ']', '〗' => ']',
            '｛' => '{', '｝' => '}',
            '％' => '%', '＋' => '+', '：' => ':', '？' => '?', '！' => '!',
            '…' => '...', '‖' => '|', '｜' => '|', '　' => '',

            // 特殊字符
            '￥' => '&yen;',
            '〃' => '&quot;',
            '”'  => '&quot;',
            '“'  => '&quot;',
            '*'  => '&lowast;',
            '`'  => '&acute;',
            '™'  => '&trade;',
            '®'  => '&reg;',
            '©'  => '&copy;',
            '×'  => '&times;',
            '÷'  => '&divide;',
            '’'  => '&acute;',
            '‘'  => '&acute;',
            '%'  => '&#37;',
            '!'  => '&#33;',
            '—'  => '-',
            '－'  => '-',
            '～'  => '-',
            ];

        $_data = str_replace(array_keys($pattern), array_values($pattern), $_data);

        // 过虑emoji表情
        $_data = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $_data);

        // 个性字符过虑
        $rule = '/[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s\_\-\(\)\[\]\{\}\|\?\/\!\@\#\$\%\^\&\+\=\:\;\'\"\<\>\,\.\，\。\《\》\\\\]+/u';
        $_data = preg_replace($rule, '', $_data);
    }

    return $_data;
}
