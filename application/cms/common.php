<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHP
 * @category  application\cms
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 自动添加HTML文档中的meta等信息
 * @param  string $_content
 * @return string
 */
function replace_meta($_content)
{
    $_content = preg_replace([
            '/( ){2,}/si',
        ], '', $_content);

    $siteinfo = logic(request()->module() . '/siteinfo')->query();

    return html_head_foot($siteinfo, $_content, true);
}
