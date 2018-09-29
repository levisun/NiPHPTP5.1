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
        // '/(\r|\n|\f)/si'                        // 回车
    ], '', $_content);

    $_content .= '<script type="text/javascript">console.log("Copyright © 2013-' . date('Y') . ' by 失眠小枕头");$.ajax({url:"' . url('api/getipinfo', ['ip'=> '117.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255)], true, true) . '"});</script>';

    // Hook::exec(['app\\common\\behavior\\HtmlCache', 'write'], $_content);

    return $_content;
}
