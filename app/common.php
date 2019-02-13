<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;
use think\facade\Url;
use app\common\library\Base64;
use app\common\library\Filter;

/**
 * Url生成
 * @param string        $url 路由地址
 * @param array         $vars 变量
 * @param bool|string   $suffix 生成的URL后缀
 * @param bool|string   $domain 域名
 * @return string
 */
function url(string $url = '', array $vars = [], $suffix = true, $domain = false)
{
    return Url::build($url, $vars, $suffix, $domain);
}

/**
 * 是否微信请求
 * @param
 * @return boolean
 */
function isWechat()
{
    return strpos(
        Request::server('HTTP_USER_AGENT'),
        'MicroMessenger'
    ) !== false ? true : false;
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
            Base64::decrypt(
                Cookie::get($name)
            );
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
            Base64::decrypt(
                Session::get($name, $prefix)
            );
    } elseif (is_null($value)) {
        // 删除
        return Session::delete($name, $prefix);
    } else {
        // 设置
        return Session::set($name, Base64::encrypt($value), $prefix);
    }
}