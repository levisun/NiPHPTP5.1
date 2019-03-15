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

use think\facade\Config;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;
use think\facade\Url;
use app\library\Base64;
use app\library\Filter;
use app\library\Image;



/**
 * 栏目授权地址
 * @param  int    $_access_id 指定权限 0为公开
 * @param  string $_url       地址
 * @return string
 */
function authorityUrl(int $_access_id, string $_url): string
{
    if ($_access_id == 0) {
        $url = url($_url, []);
    }

    elseif (session('?member_level') && session('member_level') <= $_access_id) {
        $url = url($_url, []);
    }

    else {
        $url = url('authority', ['level' => $_access_id], []);
    }

    return $url;
}

/**
 * 拼接文件地址
 * @param  string $_file 文件路径
 * @return string
 */
function fileUrl(string $_file): string
{
    $root_path = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
    $ext = pathinfo($root_path . $_file, PATHINFO_EXTENSION);
    if (in_array($ext, ['gif', 'jpg', 'jpeg', 'bmp', 'png'])) {
        return 'error';
    } else {
        return Config::get('app.cdn_host') . $_file;
    }
}

/**
 * 拼接图片地址
 * 生成缩略图
 * @param  string      $_img    图片路径
 * @param  int|integer $_width  缩略图宽
 * @param  int|integer $_height 缩略图高
 * @param  string      $_water  水印文字
 * @return string
 */
function imgUrl(string $_img, int $_width = 150, int $_height = 150, string $_water = ''): string
{
    if (!empty($_img)) {
        $img_path = trim($_img, '/');
        $img_path = str_replace('/', DIRECTORY_SEPARATOR, $img_path);

        $root_path = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR;
        $font_path = $root_path . 'static' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . 'simhei.ttf';
        $ext = pathinfo($root_path . $img_path, PATHINFO_EXTENSION);

        // 缩略图不存在,生成缩略图
        $thumb_path = str_replace('.' . $ext, '', $img_path) . '_skl_' . $_width . 'x' . $_height . '.' . $ext;
        if (!is_file($root_path . $thumb_path) && is_file($root_path . $img_path)) {
            $image = \think\Image::open($root_path . $img_path);
            if ($image->width() > $_width || $image->height() > $_height) {
                $image->thumb($_width, $_height, \think\Image::THUMB_FILLED);
                $_water = $_water ? $_water : Request::rootDomain();
                $image->text($_water, $font_path, rand(10, 20));
                $image->save($root_path . $thumb_path);
            }
            $_img = '/' . str_replace(DIRECTORY_SEPARATOR, '\\', $thumb_path);
        }

        $_img = Config::get('app.cdn_host') . $_img;
    }

    return $_img;
}

/**
 * Url生成
 * @param  string  $_url       路由地址
 * @param  array   $_vars      变量
 * @param  string  $_sub_domain 子域名
 * @return string
 */
function url(string $_url = '', array $_vars = [], string $_sub_domain = '')
{
    if (!$_sub_domain) {
        if ($referer = Request::server('HTTP_REFERER')) {
            $parse = parse_url($referer);
            $_sub_domain = str_replace(Request::rootDomain(), '', $parse['host']);
        } else {
            $_sub_domain = 'www.';
        }
    }


    $_url = Url::build('/' . $_url, $_vars, true, true);
    return str_replace('://api.', '://' . $_sub_domain, $_url);
}

/**
 * 获取语言变量值
 * @param  string $_name 语言变量名
 * @param  array  $_vars 动态变量值
 * @param  string $_lang 语言
 * @return mixed
 */
function lang(string $_name, array $_vars = [], string $_lang = '')
{
    return Lang::get($_name, $_vars, $_lang);
}

/**
 * 是否微信请求
 * @param
 * @return boolean
 */
function isWechat()
{
    return strpos(Request::server('HTTP_USER_AGENT'), 'MicroMessenger') !== false ? true : false;
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
 * API授权字符串
 * @param
 * @return string
 */
function createAuthorization()
{
    $authorization = Request::header('USER-AGENT') . Request::ip() . Env::get('root_path') . strtotime(date('Ymd'));
    $authorization = sha1(Base64::encrypt($authorization, 'authorization'));
    $authorization .= session_id() ? '.' . session_id() : '';
    return Base64::encrypt($authorization, 'authorization');
}

/**
 * Cookie管理
 * @param string|array  $_name cookie名称，如果为数组表示进行cookie设置
 * @param mixed         $_value cookie值
 * @param mixed         $_option 参数
 * @return mixed
 */
function cookie($_name, $_value = '', $_option = null)
{
    if (is_array($_name)) {
        // 初始化
        Cookie::init($_name);
    } elseif (is_null($_name)) {
        // 清除
        Cookie::clear($_value);
    } elseif ('' === $_value) {
        // 获取
        return
        0 === strpos($_name, '?') ?
            Cookie::has(substr($_name, 1), $_option) :
            Base64::decrypt(Cookie::get($_name));
    } elseif (is_null($_value)) {
        // 删除
        return Cookie::delete($_name);
    } else {
        // 设置
        return Cookie::set($_name, Base64::encrypt($_value), $_option);
    }
}

/**
 * Session管理
 * @param string|array  $_name session名称，如果为数组表示进行session设置
 * @param mixed         $_value session值
 * @param string        $_prefix 前缀
 * @return mixed
 */
function session($_name, $_value = '', $_prefix = null)
{
    if (is_array($_name)) {
        // 初始化
        Session::init($_name);
    } elseif (is_null($_name)) {
        // 清除
        Session::clear($_value);
    } elseif ('' === $_value) {
        // 判断或获取
        return
        0 === strpos($_name, '?') ?
            Session::has(substr($_name, 1), $_prefix) :
            Base64::decrypt(Session::get($_name, $_prefix));
    } elseif (is_null($_value)) {
        // 删除
        return Session::delete($_name, $_prefix);
    } else {
        // 设置
        return Session::set($_name, Base64::encrypt($_value), $_prefix);
    }
}
