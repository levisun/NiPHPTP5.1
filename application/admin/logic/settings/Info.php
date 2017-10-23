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

class Info
{

    /**
     * 查询访问数据
     * @access public
     * @param
     * @return array
     */
    public function visit()
    {
        $visit = model('Visit');

        $map = [
            ['date', '>=', strtotime('-7 days')]
        ];
        $result =
        $visit->field(true)
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
        $model = model('Config');

        $result = $model->query('SHOW TABLES FROM ' . config('database.database'));
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
            $model->table('information_schema.TABLES')
            ->field('DATA_FREE, ENGINE')
            ->where($map)
            ->find();

            $result = $result ? $result->toArray() : [];

            if ($result['DATA_FREE'] == 0) {
                continue;
            }

            $error += $result['DATA_FREE'];
        }

        return $error;
    }

    /**
     * 查询会员数据
     * @access public
     * @param
     * @return array
     */
    public function member()
    {
        $member = model('Member');

        $result['count'] =
        $member->count();

        $map = [
            ['status', '=', 0]
        ];
        $result['reg'] =
        $member->where($map)
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
        $feedback = model('Feedback');

        $result =
        $feedback->count();

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
        $message = model('Message');

        $result =
        $message->count();

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
        $link = model('Link');

        $result =
        $link->count();

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
        $ads = model('Ads');

        $map = [
            ['end_time', '>=', time()]
        ];

        $result =
        $ads->where($map)
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
        $model = model('Config');

        $result =
        $model->query('SELECT version()');

        return $result[0]['version()'];
    }
}
