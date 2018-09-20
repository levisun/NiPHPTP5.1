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

defined('APP_DEBUG') or define('APP_DEBUG', true);

/**
 * 阻挡请求
 * @return mixed
 */
function request_block()
{
    // 阻挡Ajax Pjax Post类型请求
    if (request()->isAjax() || request()->isPjax() || request()->isPost()) {
        return true;
    }

    // common模块抛出404
    $module = strtolower(request()->module());
    if ($module === 'common') {
        abort(404);
    }

    // 阻挡admin member wechat 和 空模块的请求
    if (in_array($module, ['admin', 'member', 'wechat']) || empty($module)) {
        return true;
    }

    return false;
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
    $_content = preg_replace([
        '/<\!--.*?-->/si',                      // HTML注释
        '/(\/\*).*?(\*\/)/si',                  // JS注释
        '/(\r|\n| )+(\/\/).*?(\r|\n)+/si',      // JS注释
        '/( ){2,}/si',                          // 空格
        '/(\r|\n|\f)/si'                        // 回车
    ], '', $_content);

    // $_content .= '<script type="text/javascript">console.log("Copyright © 2013-' . date('Y') . ' by 失眠小枕头");$.ajax({url:"' . url('api/getipinfo', ['ip'=> '117.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255)], true, true) . '"});</script>';

    Hook::exec(['app\\common\\behavior\\HtmlCache', 'write'], $_content);

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
    $name  = 0 === strpos($name, '?') ?
        '?' . logic('common/tools')->encrypt(substr($name, 1)) :
        logic('common/tools')->encrypt($name);
    $value = $value ? logic('common/tools')->encrypt($value) : '';

    if (is_array($name)) {
        // 初始化
        Session::init($name);
    } elseif (is_null($name)) {
        // 清除
        Session::clear($value);
    } elseif ('' === $value) {
        // 判断或获取
        return 0 === strpos($name, '?') ?
            Session::has(substr($name, 1), $prefix) :
            logic('common/tools')->decrypt(Session::get($name, $prefix));
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
    $name  =
        0 === strpos($name, '?') ?
        '?' . logic('common/tools')->encrypt(substr($name, 1)) :
        logic('common/tools')->encrypt($name);

    $value = $value ? logic('common/tools')->encrypt($value) : '';

    if (is_array($name)) {
        // 初始化
        Cookie::init($name);
    } elseif (is_null($name)) {
        // 清除
        Cookie::clear($value);
    } elseif ('' === $value) {
        // 获取
        return 0 === strpos($name, '?') ?
            Cookie::has(substr($name, 1), $option) :
            logic('common/tools')->decrypt(Cookie::get($name));
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
        $lang_path .= logic('common/tools')->safeFilter(Lang::detect(), true, true) . '.php';
        Lang::load($lang_path);

        return true;
    } elseif ($_name == ':detect') {
        return logic('common/tools')->safeFilter(Lang::detect(), true, true);
    } else {
        return Lang::get($_name, $_vars, $_lang);
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
    return logic('common/tools')->safeFilter($_content, $_hs, $_hxp, $_rn, $_sql, $_script);
}
