<?php
/**
 *
 * 应用公共函数文件
 *
 * @package   NiPHP
 * @category  application
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

use think\facade\Cookie;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;
use think\facade\Url;

defined('APP_DEBUG') or define('APP_DEBUG', true);
define('CDN_DOMAIN', '//cdn.' . Request::rootDomain() . Request::root() . '/');
define('API_DOMAIN', '//api.' . Request::rootDomain() . Request::root() . '/');
define('API_TOKEN', sha1(
    Request::server('HTTP_USER_AGENT') .
    Request::ip() .
    Env::get('root_path') .
    date('Ymd')
));

/**
 * emoji编码
 * @param  string $str
 * @return string
 */
function emoji_encode($_str){
    $encode = '';
    $length = mb_strlen($_str,'utf-8');
    for ($i=0; $i < $length; $i++) {
        $_tmpStr = mb_substr($_str, $i, 1, 'utf-8');
        if(strlen($_tmpStr) >= 4){
            $encode .= '[EMOJI:' . rawurlencode($_tmpStr) . ']';
        }else{
            $encode .= $_tmpStr;
        }
    }
    return $encode;
}

/**
 * emoji解码
 * @param  string $str
 * @return string
 */
function emoji_decode($_str)
{
    return preg_replace_callback(
        '/\[EMOJI:(.*?)\]/',
        function($matches){
            return rawurldecode($matches[1]);
        },
        $_str
    );
}

/**
 * 模板设置参数
 * @param  string $_default_theme 模板主题
 * @return array
 */
function get_template_config($_default_theme)
{
    $template = config('template.');

    $template['view_path'] = env('root_path') . 'public' .
                             DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR .
                             request()->module() . DIRECTORY_SEPARATOR .
                             $_default_theme . DIRECTORY_SEPARATOR;

    $cdn = request()->scheme() . ':' . CDN_DOMAIN;

    $template['tpl_replace_string'] = [
        '__DOMAIN__'   => request()->root(true) . '/',
        '__PHP_SELF__' => basename(request()->baseFile()),
        '__CDN__'      => $cdn,
        '__STATIC__'   => $cdn . 'static/',
        '__THEME__'    => $_default_theme,
        '__CSS__'      => $cdn . 'theme/' . request()->module() . '/' . $_default_theme . '/css/',
        '__JS__'       => $cdn . 'theme/' . request()->module() . '/' . $_default_theme . '/js/',
        '__IMG__'      => $cdn . 'theme/' . request()->module() . '/' . $_default_theme . '/images/',
    ];

    return $template;
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
        return randomCode();
    } else {
        return $code;
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
 * 是否微信请求
 * @param
 * @return boolean
 */
function is_wechat_request()
{
    return strpos(request()->server('HTTP_USER_AGENT'), 'MicroMessenger') !== false ? true : false;
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
        $lang_path .= safe_filter_strict(Lang::detect()) . '.php';
        Lang::load($lang_path);

        return true;
    } elseif ($_name == ':detect') {
        return safe_filter_strict(Lang::detect());
    } else {
        return Lang::get($_name, $_vars, $_lang);
    }
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
    $name  = !is_array($name) && 0 === strpos($name, '?') ? '?' . encrypt(substr($name, 1)) : encrypt($name);
    $value = $value ? encrypt($value) : $value;

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
    $name  = !is_array($name) && 0 === strpos($name, '?') ? '?' . encrypt(substr($name, 1)) : encrypt($name);
    $value = $value ? encrypt($value) : $value;

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
 * 字符串加密
 * @param  mixed  $_str     加密前的字符串
 * @param  string $_authkey 密钥
 * @return string           加密后的字符串
 */
function encrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $_authkey = md5($_authkey . __DIR__);

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
        $keylength = mb_strlen($_authkey);
        for ($i = 0, $count = mb_strlen($_str); $i < $count; $i += $keylength) {
            $coded .= mb_substr($_str, $i, $keylength) ^ $_authkey;
        }
        return str_replace('=', '', base64_encode($coded));
    }
}

/**
 * 解密
 * @param  mixed  $_str     加密后的字符串
 * @param  string $_authkey 密钥
 * @return string           加密前的字符串
 */
function decrypt($_str, $_authkey = '0af4769d381ece7b4fddd59dcf048da6') {
    $_authkey = md5($_authkey . __DIR__);

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
        $keylength = mb_strlen($_authkey);
        $_str = base64_decode($_str);
        for ($i = 0, $count = mb_strlen($_str); $i < $count; $i += $keylength) {
            $coded .= mb_substr($_str, $i, $keylength) ^ $_authkey;
        }
        return $coded;
    }
}

/**
 * 安全过滤
 * @param  mixed   $_content
 * @return mixed
 */
function safe_filter($_content)
{
    return logic('common/SafeFilter')->filter($_content, false);
}

/**
 * 严格安全过滤
 * @param  mixed   $_content
 * @return mixed
 */
function safe_filter_strict($_content)
{
    return logic('common/SafeFilter')->filter($_content, true);
}
