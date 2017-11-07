<?php
/**
 *
 * 系统信息 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Info.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use app\common\model\Visit as ModelVisit;
use app\common\model\Config as ModelConfig;
use app\common\model\Member as ModelMember;
use app\common\model\Feedback as ModelFeedback;
use app\common\model\Message as ModelMessage;
use app\common\model\Link as ModelLink;
use app\common\model\Ads as ModelAds;

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
        $member = $this->member();

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
                // 表错误统计
                [
                    'name'  => lang('sys table error'),
                    'value' => '<a href="' . url('expand/databack', array('method' => 'optimize')) . '">' . $this->dbTableErr() . '</a>',
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
                            'value' => $this->feedback(),
                        ],
                        [
                            'name'  => lang('message'),
                            'value' => $this->message(),
                        ],
                    ],
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
                        ],
                    ],
                ],
            ],

            // 访问统计
            'visit_info' => [
                [
                    'name'  => 'visit',
                    'value' => $this->visit(),
                ],
            ],
        ];
    }

    /**
     * 查询访问数据
     * @access public
     * @param
     * @return array
     */
    public function visit()
    {
        $model_visit = new ModelVisit;

        $map = [
            ['date', '>=', strtotime('-7 days')]
        ];
        $result =
        $model_visit->field(true)
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

        return $visit;
    }

    /**
     * 查询表错误数据
     * @access public
     * @param
     * @return int
     */
    public function dbTableErr()
    {
        $model_config = new ModelConfig;

        $result = $model_config->query('SHOW TABLES FROM ' . config('database.database'));
        $tables = array();
        foreach ($result as $key => $value) {
            $tables[] = current($value);
        }

        $error = 0;
        foreach ($tables as $key => $value) {
            $map = [
                ['TABLE_NAME', '=', $value],
            ];

            $result =
            $model_config->table('information_schema.TABLES')
            ->field('DATA_FREE, ENGINE')
            ->where($map)
            ->find();

            $result = $result ? $result->toArray() : [];

            if ($result['DATA_FREE'] == 0) {
                continue;
            }

            $error += $result['DATA_FREE'];
        }

        return $error ? $error / 1024 : $error;
    }

    /**
     * 查询会员数据
     * @access public
     * @param
     * @return array
     */
    public function member()
    {
        $model_member = new ModelMember;

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
     * @access public
     * @param
     * @return int
     */
    public function feedback()
    {
        $model_feedback = new ModelFeedback;

        $result =
        $model_feedback->count();

        return $result;
    }

    /**
     * 查询留言数据
     * @access public
     * @param
     * @return int
     */
    public function message()
    {
        $model_message = new ModelMessage;

        $result =
        $model_message->count();

        return $result;
    }

    /**
     * 查询友情链接数据
     * @access public
     * @param
     * @return int
     */
    public function link()
    {
        $model_link = new ModelLink;

        $result =
        $model_link->count();

        return $result;
    }

    /**
     * 查询广告数据
     * @access public
     * @param
     * @return int
     */
    public function ads()
    {
        $model_ads = new ModelAds;

        $map = [
            ['end_time', '>=', time()]
        ];

        $result =
        $model_ads->where($map)
        ->count();

        return $result;
    }

    /**
     * 查询数据库版本
     * @access public
     * @param
     * @return int
     */
    public function dbVersion()
    {
        $model_config = new ModelConfig;

        $result =
        $model_config->query('SELECT version()');

        return $result[0]['version()'];
    }
}
