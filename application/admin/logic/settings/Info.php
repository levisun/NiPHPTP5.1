<?php
/**
 *
 * 系统信息 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\settings;

class Info
{

    /**
     * 系统与框架版本
     * @access public
     * @param
     * @return array
     */
    public function sysinfo()
    {
        $gd_info = gd_info();
        $gd  = strtr($gd_info['GD Version'], ['bundled (' => '', ' compatible)' => '']) . '(';
        $gd .= $gd_info['GIF Read Support'] ? ' GIF' : '';
        $gd .= $gd_info['JPEG Support'] ? ' JPEG' : '';
        $gd .= $gd_info['PNG Support'] ? ' PNG' : '';
        $gd .= ')';

        $result = [
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
                'value' => config('database.type') . $this->dbVersion(),
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
            [
                'name'  => lang('sys upgrade'),
                'value' => '',
            ]
        ];

        return $result;
    }

    /**
     * 统计信息
     * @access public
     * @param
     * @return array
     */
    public function make()
    {
        $member = $this->member();

        $result = [
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
                    ]
                ]
            ],
            // 反馈与留言统计
            'feed_msg' => [
                'name'  => lang('feedback and message'),
                'value' => [
                    [
                        'name'  => lang('feedback'),
                        'value' => $this->feedback(),
                    ],
                    [
                        'name'  => lang('message'),
                        'value' => $this->message(),
                    ]
                ]
            ],
            // 广告与友情链接统计
            'ads_link' => [
                'name'  => lang('tg'),
                'value' => [
                    [
                        'name'  => lang('ads'),
                        'value' => $this->ads(),
                    ],
                    [
                        'name'  => lang('link'),
                        'value' => $this->link(),
                    ]
                ]
            ]
        ];

        return $result;
    }

    /**
     * 查询访问数据
     * @access public
     * @param
     * @return array
     */
    public function visit()
    {
        $map = [
            ['date', '>=', strtotime('-7 days')]
        ];
        $result =
        model('common/visit')->field(true)
        ->where($map)
        ->select();

        $date = $count = [];
        foreach ($result as $key => $value) {
            $value = $value->toArray();

            $date[$value['date']] = date('Y-m-d', $value['date']);
            if (empty($count[$value['date']])) {
                $count[$value['date']] = $value['count'];
            } else {
                $count[$value['date']] += $value['count'];
            }
        }
        $visit = [
            'date'  => '\'' . implode("','", $date) . '\'',
            // 'count' => '[' . implode('],[', $count) . ']'
        ];
        $num = 0;
        foreach ($count as $key => $value) {
            $visit['count'][] = '[' . date('ymd', $key) . ', ' . $value . ']';
            $num++;
        }
        if (!empty($visit['count'])) {
            $visit['count'] = implode(',', $visit['count']);
        } else {
            $visit['count'] = '';
        }

        $result = [
            'name'  => 'visit',
            'value' => $visit,
        ];

        return $result;
    }

    /**
     * 查询会员数据
     * @access private
     * @param
     * @return array
     */
    private function member()
    {
        $model_member = model('common/member');

        $result['count'] =
        $model_member->count();

        $map = [
            ['status', '=', 0]
        ];
        $result['reg'] =
        $model_member->where($map)
        ->count();

        return $result;
    }

    /**
     * 查询反馈数据
     * @access private
     * @param
     * @return int
     */
    private function feedback()
    {
        $result =
        model('common/feedback')->count();

        return $result;
    }

    /**
     * 查询留言数据
     * @access private
     * @param
     * @return int
     */
    private function message()
    {
        $result =
        model('common/message')->count();

        return $result;
    }

    /**
     * 查询友情链接数据
     * @access private
     * @param
     * @return int
     */
    private function link()
    {
        $result =
        model('common/link')->count();

        return $result;
    }

    /**
     * 查询广告数据
     * @access private
     * @param
     * @return int
     */
    private function ads()
    {
        $map = [
            ['end_time', '>=', time()]
        ];

        $result =
        model('common/ads')->where($map)
        ->count();

        return $result;
    }

    /**
     * 查询数据库版本
     * @access private
     * @param
     * @return int
     */
    private function dbVersion()
    {
        $result =
        model('common/config')->query('SELECT version()');

        return $result[0]['version()'];
    }
}
