<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application\cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 并发压力释放
 * @param
 * @return void
 */
function concurrent_error()
{
    if (rand(0, 999) === 0) {
        abort(502, '并发压力');
    }
}
