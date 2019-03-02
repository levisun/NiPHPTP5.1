<?php
/**
 *
 * 应用公共文件
 *
 * @package   NiPHP
 * @category  app
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */

use think\facade\Cookie;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;
use think\facade\Url;
use app\library\Base64;
use app\library\Filter;

/**
 * Url生成
 * @param string        $url 路由地址
 * @param array         $vars 变量
 * @return string
 */
function url(string $url = '', array $vars = [], string $sub = 'www')
{
    return '//' . $sub . '.' . Request::rootDomain() . '/' . $url . '.html';

    return '//' . $sub . '.' . Request::rootDomain() .
           Url::build($url, $vars, true, false);
}

/**
 * 获取语言变量值
 * @param string    $name 语言变量名
 * @param array     $vars 动态变量值
 * @param string    $lang 语言
 * @return mixed
 */
function lang(string $name, array $vars = [], string $lang = '')
{
    return Lang::get($name, $vars, $lang);
}

/**
 * 是否微信请求
 * @param
 * @return boolean
 */
function isWechat()
{
    return
    strpos(Request::server('HTTP_USER_AGENT'), 'MicroMessenger') !== false ? true : false;
}

/**
 * 安全过滤
 * @param  [type] $_data [description]
 * @return [type]        [description]
 */
function safeFilter($_data)
{
    return Filter::default($_data, true);
}

/**
 * Cookie管理
 * @param string|array  $name cookie名称，如果为数组表示进行cookie设置
 * @param mixed         $value cookie值
 * @param mixed         $option 参数
 * @return mixed
 */
function cookie($name, $value = '', $option = null)
{
    if (is_array($name)) {
        // 初始化
        Cookie::init($name);
    } elseif (is_null($name)) {
        // 清除
        Cookie::clear($value);
    } elseif ('' === $value) {
        // 获取
        return
        0 === strpos($name, '?') ?
            Cookie::has(substr($name, 1), $option) :
            Base64::decrypt(Cookie::get($name));
    } elseif (is_null($value)) {
        // 删除
        return Cookie::delete($name);
    } else {
        // 设置
        return Cookie::set($name, Base64::encrypt($value), $option);
    }
}

/**
 * Session管理
 * @param string|array  $name session名称，如果为数组表示进行session设置
 * @param mixed         $value session值
 * @param string        $prefix 前缀
 * @return mixed
 */
function session($name, $value = '', $prefix = null)
{
    if (is_array($name)) {
        // 初始化
        Session::init($name);
    } elseif (is_null($name)) {
        // 清除
        Session::clear($value);
    } elseif ('' === $value) {
        // 判断或获取
        return
        0 === strpos($name, '?') ?
            Session::has(substr($name, 1), $prefix) :
            Base64::decrypt(Session::get($name, $prefix));
    } elseif (is_null($value)) {
        // 删除
        return Session::delete($name, $prefix);
    } else {
        // 设置
        return Session::set($name, Base64::encrypt($value), $prefix);
    }
}
