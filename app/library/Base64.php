<?php
/**
 *
 * 服务层
 * 加密类
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

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
        if (function_exists($_type)) {
            // 第一次加密
            $_str = trim($_str);
            $_str = call_user_func($_type, $_str);

            // 第二次加密
            $_str = sha1($_str . $_salt . $_type);

            // 第三次加密
            return call_user_func($_type, $_str . $_salt . $_type);
        } else {
            throw new HttpException(502, '参数错误');
        }
    }

    /**
     * 生成旗标
     * @param  string      $_authkey
     * @param  int|integer $_length
     * @return string
     */
    public static function flag($_authkey = '', int $_length = 7)
    {
        $_authkey = sha1(__DIR__ . $_authkey);
        $_length = $_length > 40 ? 40 : $_length;
        return substr(sha1($_authkey), 0, $_length);
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
        $_authkey = sha1(__DIR__ . $_authkey);

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
        $_authkey = sha1(__DIR__ . $_authkey);

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
