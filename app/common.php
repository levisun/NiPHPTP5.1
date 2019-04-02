<?php
/**
 *
 * 应用公共文件
 *
 * @package   NICMS
 * @category  app
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */

use think\Image;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;
use think\facade\Url;
use app\library\Base64;
use app\library\Filter;



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
    $root_path = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
    $ext = pathinfo($root_path . $_file, PATHINFO_EXTENSION);

    if (in_array($ext, ['gif', 'jpg', 'jpeg', 'bmp', 'png'])) {
        return 'error';
    } else {
        // $ext = '.' . $ext;
        // $newname = $root_path . str_replace($ext, '', $_file) . '_skl_' . $ext;
        // if (!is_file($newname)) {
        //     rename($root_path . $_file, $newname);
        // }
        return Config::get('app.cdn_host') . $_file;
    }
}

/**
 * 拼接图片地址
 * 生成缩略图
 * @param  string      $_img   图片路径
 * @param  int|integer $_size  缩略图宽高
 * @param  string      $_water 水印文字
 * @return string
 */
function imgUrl(string $_img, int $_size = 200, string $_water = ''): string
{
    $root_path = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
    $font_path = $root_path . 'static' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . 'simhei.ttf';

    if ($_img && stripos($_img, 'http') === false) {
        // 规定缩略图大小
        $_size = $_size >= 800 ? 800 : round($_size / 100) * 100;
        $_size = (int) $_size;

        // URL路径转换目录路径
        $img_path = trim($_img, '/');
        $img_path = str_replace('/', DIRECTORY_SEPARATOR, $img_path);
        $img_ext = '.' . pathinfo($root_path . $img_path, PATHINFO_EXTENSION);

        // 修正原始图片名
        $new_img = str_replace($img_ext, '_skl_' . $img_ext, $img_path);
        if (!is_file($root_path . $new_img)) {
            rename($root_path . $img_path, $root_path . $new_img);
        }
        $img_path = $new_img;
        unset($new_img);

        if (is_file($root_path . $img_path) && $_size) {
            $thumb_path = str_replace($img_ext, '', $img_path) . $_size . 'x' . $_size . $img_ext;
            if (!is_file($root_path . $thumb_path)) {

                // 修正原始图片名带尺寸
                $image = Image::open($root_path . $img_path);
                $newname = str_replace($img_ext, '', $img_path) . $image->width() . 'x' . $image->height() . $img_ext;
                if (!is_file($root_path . $newname)) {
                    $_water = $_water ? $_water : Request::rootDomain();
                    $image->text($_water, $font_path, 15, '#00000000', Image::WATER_SOUTHEAST);
                    $image->save($root_path . $newname, null, 50);
                }
                unset($image);

                // 原始尺寸大于指定缩略尺寸,生成缩略图
                $image = Image::open($root_path . $img_path);
                if ($image->width() > $_size) {
                    $image->thumb($_size, $_size, Image::THUMB_SCALING);
                }

                // 添加水印
                $_water = $_water ? $_water : Request::rootDomain();
                $image->text($_water, $font_path, 15, '#00000000', Image::WATER_SOUTHEAST);

                $image->save($root_path . $thumb_path, null, 40);
                unset($image);
            }

            $_img = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $thumb_path);
        } elseif (is_file($root_path . $img_path)) {
            $_img = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $img_path);
        } else {
            $_img = Config::get('app.default_img');
        }
    }

    return Config::get('app.cdn_host') . $_img;
}

function formatNumber(int $_number, string $_type = 'date')
{
    if ($_type == 'date') {
        if ($_number >= 31104000) {
            $format = ceil($_number / 31104000) . '年前';
        } elseif ($_number >= 2592000) {
            $format = ceil($_number / 2592000) . '月前';
        } elseif ($_number >= 86400) {
            $format = ceil($_number / 86400) . '日前';
        } elseif ($_number >= 3600) {
            $format = ceil($_number / 3600) . '时前';
        } else {
            $format = ceil($_number / 60) . '分前';
        }
    }

    elseif ($_type == 'number') {
        if ($_number >= 10000000) {
            $format = number_format($_number / 10000000, 2) . '千万';
        } elseif ($_number >= 10000) {
            $format = number_format($_number / 10000, 2) . '万';
        } elseif ($_number >= 1000) {
            $format = number_format($_number / 1000, 2) . '千';
        } elseif ($_number >= 100) {
            $format = number_format($_number / 100, 2) . '百';
        } else {
            $format = $_number;
        }
    }
}

/**
 * Emoji原形转换为String
 * @param  string $_str
 * @return string
 */
function emojiEncode($_str): string
{
    return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($string) {
        return addslashes($string[0]);
    }, json_encode($_str)));
}

/**
 * Emoji字符串转换为原形
 * @param  string $_str
 * @return string
 */
function emojiDecode($_str): string
{
    return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
        return '\\';
    }, json_encode($_str)));
}

/**
 * Emoji字符串清清理
 * @param string $_str
 * @return string
 */
function emojiClear($_str): string
{
    return preg_replace_callback('/./u', function (array $match) {
        return strlen($match[0]) >= 4 ? '' : $match[0];
    }, $_str);
}

/**
 * Url生成
 * @param  string  $_url       路由地址
 * @param  array   $_vars      变量
 * @param  string  $_sub_domain 子域名
 * @return string
 */
function url(string $_url = '', array $_vars = [], string $_sub_domain = ''): string
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
    $_url = str_replace(Request::scheme() . ':', '', $_url);
    return str_replace('//api.', '//' . $_sub_domain, $_url);
}

/**
 * 获取语言变量值
 * @param  string $_name 语言变量名
 * @param  array  $_vars 动态变量值
 * @param  string $_lang 语言
 * @return mixed
 */
function lang(string $_name, array $_vars = [], string $_lang = ''): string
{
    return Lang::get($_name, $_vars, $_lang);
}

/**
 * 是否微信请求
 * @param
 * @return boolean
 */
function isWechat(): bool
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
function createAuthorization(): string
{
    $authorization = Request::header('USER-AGENT') . Request::ip() . app()->getRootPath() . strtotime(date('Ymd'));
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
