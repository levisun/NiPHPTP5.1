<?php
/**
 *
 * 模板设置
 *
 * @package   NiPHPCMS
 * @category  config
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: template.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */

return [
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 布局
    'layout_on'    => true,
    // 布局入口文件名
    'layout_name'  => 'layout',
    // 布局输出替换变量
    'layout_item'  => '{__CONTENT__}',
];
