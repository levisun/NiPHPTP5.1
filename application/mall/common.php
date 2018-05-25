<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application\mall
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 分转元
 * @param  intval $value
 * @return string
 */
function to_yen($value, $param = true)
{
    if (empty($value)) {
        $value = 0;
    }

    if ($param) {
        $value = number_format((float) $value / 100, 2, '.', '');
        return '&yen;' . $value;
    } else {
        $strtr = ['&yen;' => '', '¥' => '', '￥' => '', '元' => ''];
        $value = strtr($value, $strtr);
        $value = (float) $value;
        return $value * 100;
    }
}

/**
 * 生成订单号
 * @return string
 */
function order_no()
{
    list($micro, $time) = explode(' ', microtime());
    $micro = str_pad($micro * 1000000, 6, 0, STR_PAD_LEFT);
    return substr($time, 0, 7) . date('YmdHis') . $micro . mt_rand(11111, 99999);
}
