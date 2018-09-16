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

use think\facade\Lang;

defined('APP_DEBUG') or define('APP_DEBUG', false);

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
    if (!APP_DEBUG && request()->isGet() && rand(1, 100) === 1) {
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
    return strpos(request()->header('user-agent'), 'MicroMessenger') !== false ? true : false;
}

/**
 * 模板过滤
 * @param  string $_content
 * @return string
 */
function view_filter($_content)
{
    if (!APP_DEBUG) {
        $_content .= '<script type="text/javascript">
        console.log("Copyright © 2013-' . date('Y') . ' by 失眠小枕头");
        </script>';
    }

    $_content = preg_replace([
        '/<\!--.*?-->/si',                      // HTML注释
        '/(\/\*).*?(\*\/)/si',                  // JS注释
        '/(\r|\n| )+(\/\/).*?(\r|\n)+/si',      // JS注释
        '/( ){2,}/si',                          // 空格
        '/(\r|\n|\f)/si'                        // 回车
    ], '', $_content);

    // $_content .= '<script type="text/javascript">$.ajax({url:"' . url('api/getipinfo', ['ip'=> '117.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255)], true, true) . '"});</script>';

    // Hook::exec(['app\\common\\behavior\\HtmlCacheBehavior', 'write'], $_content);

    return $_content;
}

/**
 * Session管理
 * 数据加密
 * @param  string|array  $name session名称，如果为数组表示进行session设置
 * @param  mixed         $value session值
 * @param  string        $prefix 前缀
 * @return mixed
 */
function session($name, $value = '', $prefix = null)
{
    $name  = 0 === strpos($name, '?') ? '?' . encrypt(substr($name, 1)) : encrypt($name);
    $value = $value ? encrypt($value) : '';

    if (is_array($name)) {
        // 初始化
        Session::init($name);
    } elseif (is_null($name)) {
        // 清除
        Session::clear($value);
    } elseif ('' === $value) {
        // 判断或获取
        return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : decrypt(Session::get($name, $prefix));
    } elseif (is_null($value)) {
        // 删除
        return Session::delete($name, $prefix);
    } else {
        // 设置
        return Session::set($name, $value, $prefix);
    }
}

/**
 * Cookie管理
 * 数据加密
 * @param  string|array  $name cookie名称，如果为数组表示进行cookie设置
 * @param  mixed         $value cookie值
 * @param  mixed         $option 参数
 * @return mixed
 */
function cookie($name, $value = '', $option = null)
{
    $name  = 0 === strpos($name, '?') ? '?' . encrypt(substr($name, 1)) : encrypt($name);
    $value = $value ? encrypt($value) : '';

    if (is_array($name)) {
        // 初始化
        Cookie::init($name);
    } elseif (is_null($name)) {
        // 清除
        Cookie::clear($value);
    } elseif ('' === $value) {
        // 获取
        return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : decrypt(Cookie::get($name));
    } elseif (is_null($value)) {
        // 删除
        return Cookie::delete($name);
    } else {
        // 设置
        return Cookie::set($name, $value, $option);
    }
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

        // 加载对应语言包
        $lang_path  = env('app_path') . request()->module();
        $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
        $lang_path .= Lang::detect() . '.php';
        Lang::load($lang_path);

        return true;
    } elseif ($_name == ':detect') {
        return safe_filter(Lang::detect(), true, true);
    } else {
        return Lang::get($_name, $_vars, $_lang);
    }
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
    return  md5($_password . $_salt);
}

/**
 * 运行时间与占用内存
 * @param  boolean $_start
 * @return mixed
 */
function use_time_memory()
{
    $runtime = number_format(microtime(true) - app()->getBeginTime(), 10);
    $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
    $mem     = number_format((memory_get_usage() - app()->getBeginMem()) / 1024, 2);

    return [
        '运行时间：' . number_format($runtime, 6) . 's 吞吐率：' . $reqs . 'req/s 内存消耗：' . $mem . 'kb 文件加载：' . count(get_included_files()),
        '查询信息：' . Db::$queryTimes . ' queries ' . Db::$executeTimes . ' writes',
        '缓存信息：' . app('cache')->getReadTimes() . ' reads ' . app('cache')->getWriteTimes() . ' writes',
    ];
}

/**
 * 清除运行垃圾文件
 * @param
 * @return void
 */
remove_rundata();
function remove_rundata()
{
    // 减少频繁操作,每次请求百分之一几率运行操作
    if (rand(1, 100) !== 1) {
        return false;
    }

    // 禁止GET以外请求操作
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        return false;
    }

    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;

    $files = ['cache', 'log', 'temp'];

    $dir_path = [];
    foreach ($files as $key => $value) {
        $dir_path = array_merge($dir_path, (array) glob($dir . $value . DIRECTORY_SEPARATOR . '*'));
    }

    $all_files = [];
    foreach ($dir_path as $key => $path) {
        if (is_file($path)) {
            $all_files[] = $path;
        } elseif (is_dir($path . DIRECTORY_SEPARATOR)) {
            $temp = (array) glob($path . DIRECTORY_SEPARATOR . '*');
            if (!empty($temp)) {
                $all_files = array_merge($all_files, $temp);
            } else {
                $all_files[] = $path;
            }
        }
    }

    // 过滤未过期文件与目录
    $days = APP_DEBUG ? strtotime('-8 hour') : strtotime('-7 days');
    foreach ($all_files as $key => $path) {
        if (is_file($path)) {
            if (filectime($path) >= $days) {
                unset($all_files[$key]);
            }
        } elseif (is_dir($path)) {
            if (filectime($path) >= $days) {
                unset($all_files[$key]);
            }
        }
    }

    // 为空
    if (empty($all_files)) {
        return false;
    }

    // 随机抽取1000条信息
    shuffle($all_files);
    $all_files = array_slice($all_files, 0, 1000);

    foreach ($all_files as $path) {
        if (is_file($path)) {
            @unlink($path);
        } elseif (is_dir($path)) {
            @rmdir($path);
        }
    }
}

/**
 * 字符串加密
 * @param  mixed  $_str     加密前的字符串
 * @param  string $_authkey 密钥
 * @return string           加密后的字符串
 */
function encrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $_authkey = md5($_authkey . env('app_path'));
    if (is_array($_str)) {
        $en = [];
        foreach ($_str as $key => $value) {
            $en[encrypt($key)] = encrypt($value, $_authkey);
        }
        return $en;
    } elseif(is_bool($_str) || is_null($_str)) {
        return $_str;
    } else {
        $coded = '';
        $keylength = strlen($_authkey);
        for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
            $coded .= substr($_str, $i, $keylength) ^ $_authkey;
        }
        return str_replace('=', '', base64_encode($coded));
    }
}

/**
 * 字符串解密
 * @param  mixed  $_str     加密后的字符串
 * @param  string $_authkey 密钥
 * @return string           加密前的字符串
 */
function decrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $_authkey = md5($_authkey . env('app_path'));
    if (is_array($_str)) {
        $de = [];
        foreach ($_str as $key => $value) {
            $de[decrypt($key)] = decrypt($value, $_authkey);
        }
        return $de;
    } elseif(is_bool($_str) || is_null($_str)) {
        return $_str;
    } else {
        $coded = '';
        $keylength = strlen($_authkey);
        $_str = base64_decode($_str);
        for ($i = 0, $count = strlen($_str); $i < $count; $i += $keylength) {
            $coded .= substr($_str, $i, $keylength) ^ $_authkey;
        }
        return $coded;
    }
}

/**
 * 安全过滤
 * @param  mixed   $_content
 * @param  boolean $_hs      HTML转义 默认false
 * @param  boolean $_hxp     HTML XML PHP标签过滤 默认false
 * @param  boolean $_rn      回车换行空格过滤 默认true
 * @param  boolean $_script  JS脚本过滤 默认true
 * @param  boolean $_sql     SQL关键词过滤 默认true
 * @return mixed
 */
function safe_filter($_content, $_hs = false, $_hxp = false, $_rn = true, $_sql = true, $_script = true)
{
    if (is_array($_content)) {
        foreach ($_content as $key => $value) {
            $_content[trim($key)] = safe_filter($value, $_hs, $_hxp, $_script, $_sql);
        }
        return $_content;
    } else {
        // 过滤前后空格
        $_content = trim($_content);

        //特殊字符过滤
        $pattern = [
            // 全角转半角
            '０'=>'0','１'=>'1','２'=>'2','３'=>'3','４'=>'4','５'=>'5','６'=>'6','７'=>'7','８'=>'8','９'=>'9','Ａ'=>'A','Ｂ'=>'B','Ｃ'=>'C','Ｄ'=>'D','Ｅ'=>'E','Ｆ'=>'F','Ｇ'=>'G','Ｈ'=>'H','Ｉ'=>'I','Ｊ'=>'J','Ｋ'=>'K','Ｌ'=>'L','Ｍ'=>'M','Ｎ'=>'N','Ｏ'=>'O','Ｐ'=>'P','Ｑ'=>'Q','Ｒ'=>'R','Ｓ'=>'S','Ｔ'=>'T','Ｕ'=>'U','Ｖ'=>'V','Ｗ'=>'W','Ｘ'=>'X','Ｙ'=>'Y','Ｚ'=>'Z','ａ'=>'a','ｂ'=>'b','ｃ'=>'c','ｄ'=>'d','ｅ'=>'e','ｆ'=>'f','ｇ'=>'g','ｈ'=>'h','ｉ'=>'i','ｊ'=>'j','ｋ'=>'k','ｌ'=>'l','ｍ'=>'m','ｎ'=>'n','ｏ'=>'o','ｐ'=>'p','ｑ'=>'q','ｒ'=>'r','ｓ'=>'s','ｔ'=>'t','ｕ'=>'u','ｖ'=>'v','ｗ'=>'w','ｘ'=>'x','ｙ'=>'y','ｚ'=>'z',
            '〔'=>'[','【'=>'[','〖'=>'[','〕'=>']','】'=>']','〗'=>']',

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

            // 特殊字符
            '+' => '&#43;', '—' => '&ndash;', '×' => '&times;', '÷' => '&divide;',
            '‖' => '&#124;',
            '“' => '&ldquo;', '”' => '&rdquo;',
            '‘' => '&lsquo;', '’' => '&rsquo;',
            '™' => '&trade;', '®' => '&reg;', '©' => '&copy;',
            '℃' => '&#8451;', '℉' => '&#8457;',

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

        $_content = str_replace(array_keys($pattern), array_values($pattern), $_content);

        // 过滤非法标签
        $_content = preg_replace([
            '/<\?php(.*?)\?>/si',
            '/<\?(.*?)\?>/si',
            '/<%(.*?)%>/si',
            '/<\?php|<\?|\?>|<%|%>/si',
        ], '', $_content);

        // 过滤JS脚本
        if ($_script === true || $_script === 'script' || $_script === 'js') {
            $_content = preg_replace([
                '/on([a-zA-Z0-9]*?)(=)["|\'](.*?)["|\']/si',
                '/(javascript:)(.*?)(\))/si',
                '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si',
                '/<(\/?javascript.*?)>/si',
                '/<(script.*?)>(.*?)<(\/script.*?)>/si',
                '/<(\/?script.*?)>/si',
                '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',
                '/<(\/?applet.*?)>/si',
                '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',
                '/<(\/?vbscript.*?)>/si',
                '/<(expression.*?)>(.*?)<(\/expression.*?)>/si',
                '/<(\/?expression.*?)>/si',
            ], '', $_content);
        }

        // 过滤SQL关键词
        if ($_sql === true || $_sql === 'sql') {
            $pattern = [
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

                '/(\%)+/si'  => '&#37;', '/(\!)+/si'  => '&#33;',
                /*'/(=)+/si'  => '&#61;', '/(\-)+/si' => '&ndash;',*/ '/(\+)+/si' => '&#43;', '/(\*)+/si'  => '&#42;',
                '/(\:)+/si'  => '&#58;', '/(\()+/si'  => '&#40;', '/(\))+/si'  => '&#41;',
                // '\'' => '&#039;',

            //
            // '@'  => '&#64;',
            //
            // '?'  => '&#129;',
            // '+'  => '&#43;',
            // ':'  => '&#58;',
            ];
            $_content = preg_replace(array_keys($pattern), array_values($pattern), $_content);
        }

        // 回车换行空格
        if ($_rn === true || $_rn === 'rn') {
            $pattern = [
                '/( ){2,}/si'    => '',
                '/[\r\n\f]+</si' => '<',
                '/>[\r\n\f]+/si' => '>',
            ];
            $_content = preg_replace(array_keys($pattern), array_values($pattern), $_content);
        }

        // 过滤HTML XML PHP标签
        if ($_hxp === true || $_hxp === 'hxp') {
            $_content = strip_tags($_content);
        } else {
            $_content = preg_replace([
                // 过滤HTML嵌入
                '/<(html.*?)>(.*?)<(\/html.*?)>/si',
                '/<(\/?html.*?)>/si',
                '/<(head.*?)>(.*?)<(\/head.*?)>/si',
                '/<(\/?head.*?)>/si',
                '/<(title.*?)>(.*?)<(\/title.*?)>/si',
                '/<(\/?title.*?)>/si',
                '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',
                '/<(\/?meta.*?)>/si',
                '/<(body.*?)>(.*?)<(\/body.*?)>/si',
                '/<(\/?body.*?)>/si',
                '/<(style.*?)>(.*?)<(\/style.*?)>/si',
                '/<(\/?style.*?)>/si',
                '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',
                '/<(\/?iframe.*?)>/si',
                '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',
                '/<(\/?frame.*?)>/si',
                '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si',
                '/<(\/?frameset.*?)>/si',
                '/<(base.*?)>(.*?)<(\/base.*?)>/si',
                '/<(\/?base.*?)>/si',

                // 过滤HTML危害标签信息
                '/<(object.*?)>(.*?)<(\/object.*?)>/si',
                '/<(\/?object.*?)>/si',
                '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',
                '/<(\/?xml.*?)>/si',
                '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',
                '/<(\/?blink.*?)>/si',
                '/<(link.*?)>(.*?)<(\/link.*?)>/si',
                '/<(\/?link.*?)>/si',
                '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',
                '/<(\/?embed.*?)>/si',
                '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',
                '/<(\/?ilayer.*?)>/si',
                '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',
                '/<(\/?layer.*?)>/si',
                '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',
                '/<(\/?bgsound.*?)>/si',
                '/<(form.*?)>(.*?)<(\/form.*?)>/si',
                '/<(\/?form.*?)>/si',

                '/<\!--.*?-->/si',
            ], '', $_content);
        }

        // HTML转义
        if ($_hs === true || $_hs === 'hs') {
            $_content = htmlspecialchars($_content);
        }
    }

    return $_content;
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
    } elseif (is_string($_data)) {
        $_data = preg_replace([
            // 过滤非法标签
            '/<\?php(.*?)\?>/si',
            '/<\?(.*?)\?>/si',
            '/<%(.*?)%>/si',
            '/<\?php|<\?|\?>|<%|%>/si',

            // 过滤XXE注入
            '/<(\!ENTITY.*?)>/si',
            '/<(\!DOCTYPE.*?)>/si',
            '/<(\!.*?)>/si',

            // 过滤JS注入
            '/on([a-zA-Z]*?)(=)["|\'](.*?)["|\']/si',
            '/(javascript:)(.*?)(\))/si',
            '/<(javascript.*?)>(.*?)<(\/javascript.*?)>/si',
            '/<(\/?javascript.*?)>/si',
            '/<(script.*?)>(.*?)<(\/script.*?)>/si',
            '/<(\/?script.*?)>/si',
            '/<(applet.*?)>(.*?)<(\/applet.*?)>/si',
            '/<(\/?applet.*?)>/si',
            '/<(vbscript.*?)>(.*?)<(\/vbscript.*?)>/si',
            '/<(\/?vbscript.*?)>/si',
            '/<(expression.*?)>(.*?)<(\/expression.*?)>/si',
            '/<(\/?expression.*?)>/si',

            // 过滤HTML嵌入
            '/<(html.*?)>(.*?)<(\/html.*?)>/si',
            '/<(\/?html.*?)>/si',
            '/<(head.*?)>(.*?)<(\/head.*?)>/si',
            '/<(\/?head.*?)>/si',
            '/<(title.*?)>(.*?)<(\/title.*?)>/si',
            '/<(\/?title.*?)>/si',
            '/<(meta.*?)>(.*?)<(\/meta.*?)>/si',
            '/<(\/?meta.*?)>/si',
            '/<(body.*?)>(.*?)<(\/body.*?)>/si',
            '/<(\/?body.*?)>/si',
            '/<(style.*?)>(.*?)<(\/style.*?)>/si',
            '/<(\/?style.*?)>/si',
            '/<(iframe.*?)>(.*?)<(\/iframe.*?)>/si',
            '/<(\/?iframe.*?)>/si',
            '/<(frame.*?)>(.*?)<(\/frame.*?)>/si',
            '/<(\/?frame.*?)>/si',
            '/<(frameset.*?)>(.*?)<(\/frameset.*?)>/si',
            '/<(\/?frameset.*?)>/si',
            '/<(base.*?)>(.*?)<(\/base.*?)>/si',
            '/<(\/?base.*?)>/si',

            // 过滤HTML危害标签信息
            '/<(object.*?)>(.*?)<(\/object.*?)>/si',
            '/<(\/?object.*?)>/si',
            '/<(xml.*?)>(.*?)<(\/xml.*?)>/si',
            '/<(\/?xml.*?)>/si',
            '/<(blink.*?)>(.*?)<(\/blink.*?)>/si',
            '/<(\/?blink.*?)>/si',
            '/<(link.*?)>(.*?)<(\/link.*?)>/si',
            '/<(\/?link.*?)>/si',
            '/<(embed.*?)>(.*?)<(\/embed.*?)>/si',
            '/<(\/?embed.*?)>/si',
            '/<(ilayer.*?)>(.*?)<(\/ilayer.*?)>/si',
            '/<(\/?ilayer.*?)>/si',
            '/<(layer.*?)>(.*?)<(\/layer.*?)>/si',
            '/<(\/?layer.*?)>/si',
            '/<(bgsound.*?)>(.*?)<(\/bgsound.*?)>/si',
            '/<(\/?bgsound.*?)>/si',

            '/<\!--.*?-->/si',
        ], '', $_data);

        $pattern = [
            // 多余回车
            '/[\r\n\f]+</si' => '<',
            '/>[\r\n\f]+/si' => '>',

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
            '%'  => '&#37;',
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
    }

    return $_data;


    // 过虑emoji表情 替换成*
    // $value = json_encode($_data);
    // $value = preg_replace('/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/', '&#42;', $value);
    // $_data = json_decode($value);

    // 个性字符过虑
    // $rule = '/[^\x{4e00}-\x{9fa5}a-zA-Z0-9\s\_\-\(\)\[\]\{\}\|\?\/\!\@\#\$\%\^\&\+\=\:\;\'\"\<\>\,\.\，\。\《\》\\\\]+/u';
    // $_data = preg_replace($rule, '', $_data);
}
