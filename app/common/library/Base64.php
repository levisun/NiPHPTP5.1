<?php
/**
 *
 * 加密类 - 方法库
 *
 * @package   NiPHP
 * @category  app\common\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\common\library;

use think\exception\HttpException;

class Base64
{

    /**
     * 密码加密
     * 不可逆
     * @access public
     * @static
     * @param  string $_str
     * @param  string $_salt
     * @param  string $_type
     * @return string
     */
    public static function password(string $_str, string $_salt = '', string $_type = 'md5'): string
    {
        $_salt = sha1($_salt . AUTHKEY);

        if (function_exists($_type)) {
            $_str = trim($_str);
            $_str = base64_encode($_str . $_salt . $_type);
            $_str = call_user_func($_type, $_str . $_salt);
            return $_str;
        } else {
            throw new HttpException(502, '参数错误');
        }
    }

    /**
     * 数据加密
     * @access public
     * @static
     * @param  mixed  $_data    加密前的数据
     * @param  string $_authkey 密钥
     * @return mixed            加密后的数据
     */
    public static function encrypt($_data, string $_authkey = '')
    {
        $_authkey = sha1($_authkey . AUTHKEY);

        if (is_array($_data)) {
            $encrypt = [];
            foreach ($_data as $key => $value) {
                $encrypt[self::encrypt($key, $_authkey)] = self::encrypt($value, $_authkey);
            }
        } elseif (is_null($_data) || is_bool($_data)) {
            $encrypt = $_data;
        } else {
            $encrypt = '';
            $length = strlen($_authkey);
            $_data = (string) $_data;
            for ($i = 0, $count = mb_strlen($_data, 'utf-8'); $i < $count; $i+=$length) {
                $encrypt .= mb_substr($_data, $i, $length, 'utf-8') ^ $_authkey;
            }
            $encrypt = str_replace('=', '', base64_encode($encrypt));
        }

        return $encrypt;
    }

    /**
     * 数据解密
     * @access public
     * @static
     * @param  mixed  $_data    加密前的数据
     * @param  string $_authkey 密钥
     * @return mixed            加密后的数据
     */
    public static function decrypt($_data, string $_authkey = '')
    {
        $_authkey = sha1($_authkey . AUTHKEY);

        if (is_array($_data)) {
            $encrypt = [];
            foreach ($_data as $key => $value) {
                $encrypt[self::decrypt($key, $_authkey)] = self::decrypt($value, $_authkey);
            }
        } elseif (is_null($_data) || is_bool($_data)) {
            $encrypt = $_data;
        } else {
            $encrypt = '';
            $length = strlen($_authkey);
            $_data = base64_decode($_data);
            for ($i = 0, $count = mb_strlen($_data, 'utf-8'); $i < $count; $i += $length) {
                $encrypt .= mb_substr($_data, $i, $length, 'utf-8') ^ $_authkey;
            }
        }

        return $encrypt;
    }
}
