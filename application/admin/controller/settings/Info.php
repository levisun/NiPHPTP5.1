<?php
/**
 *
 * 系统信息 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Info.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Info
{

    /**
     * 系统信息
     * @access public
     * @param
     * @return array
     */
    public function info()
    {
        $info = logic('Info', 'logic\settings');

        return [
            // 操作系统
            'os'          => PHP_OS,
            // 运行环境
            'env'         => request()->server('SERVER_SOFTWARE'),
            // 框架版本
            'tp_version'  => App()->version(),
            // 数据库类型
            'db_type'     => config('database.type'),
            // 数据库版本
            'db_version'  => $info->dbVersion(),
            // 访问统计
            'visit'       => $info->visit(),
            // 表错误统计
            'table_error' => $info->dbTableErr(),
            // 会员统计
            'member'      => $info->member(),
            // 反馈统计
            'feedback'    => $info->feedback(),
            // 留言统计
            'message'     => $info->message(),
            // 友情链接统计
            'link'        => $info->link(),
            // 广告统计
            'ads'         => $info->ads(),
        ];
    }
}
