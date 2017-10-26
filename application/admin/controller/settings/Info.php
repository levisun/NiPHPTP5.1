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

        $member = $info->member();

        $gd_info = gd_info();
        $gd  = strtr($gd_info['GD Version'], ['bundled (' => '', ' compatible)' => '']) . '(';
        $gd .= $gd_info['GIF Read Support'] ? ' GIF' : '';
        $gd .= $gd_info['JPEG Support'] ? ' JPEG' : '';
        $gd .= $gd_info['PNG Support'] ? ' PNG' : '';
        $gd .= ')';

        return [
            'sys_info' => [
                // 系统与框架版本
                [
                    'name'  => lang('sys version'),
                    'value' => 'NC' . NP_VERSION . ' TP' . App()->version(),
                ],
                // 操作系统
                [
                    'name'  => lang('sys os'),
                    'value' => PHP_OS,
                ],
                // 运行环境
                [
                    'name'  => lang('sys env'),
                    'value' => request()->server('SERVER_SOFTWARE'),
                ],
                // 数据库类型与版本
                [
                    'name'  => lang('sys db'),
                    'value' => config('database.type') . $info->dbVersion(),
                ],
                [
                    'name'  => 'GD',
                    'value' => $gd,
                ],
                [
                    'name'  => lang('sys timezone'),
                    'value' => config('default_timezone'),
                ],
                [
                    'name'  => lang('sys copy'),
                    'value' => '失眠小枕头 [levisun.mail@gmail.com]',
                ],
                // 表错误统计
                [
                    'name'  => lang('sys table error'),
                    'value' => '<a href="' . url('expand/databack', array('method' => 'optimize')) . '">' . $info->dbTableErr() . '</a>',
                ],
                [
                    'name'  => lang('sys upgrade'),
                    'value' => '',
                ],
            ],
            'sys_make' => [
                // 会员统计
                'member' => [
                    'name'  => lang('member'),
                    'value' => [
                        [
                            'name'  => lang('member count'),
                            'value' => $member['count'],
                        ],
                        [
                            'name'  => lang('member reg'),
                            'value' => $member['reg'],
                        ],
                    ],
                ],
                // 反馈与留言统计
                'feed_msg' => [
                    'name'  => lang('feedback and message'),
                    'value' => [
                        [
                            'name'  => lang('feedback'),
                            'value' => $info->feedback(),
                        ],
                        [
                            'name'  => lang('message'),
                            'value' => $info->message(),
                        ],
                    ],
                ],
                // 广告与友情链接统计
                'ads_link' => [
                    'name'  => lang('tg'),
                    'value' => [
                        [
                            'name'  => lang('ads'),
                            'value' => $info->ads(),
                        ],
                        [
                            'name'  => lang('link'),
                            'value' => $info->link(),
                        ],
                    ],
                ],
            ],

            // 访问统计
            'visit_info' => [
                [
                    'name'  => 'visit',
                    'value' => $info->visit(),
                ],
            ],
        ];
    }
}
