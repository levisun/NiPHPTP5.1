<?php
/**
 *
 * 并发 - 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class Concurrent
{
    /**
     * 并发压力
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        if (APP_DEBUG) {
            return false;
        }

        // 拦截频繁请求
        // 拦截非法请求
        if ($this->intercept()) {
            abort(502);
        }

        // 阻挡Ajax Pjax Post类型请求
        // 阻挡common模块请求
        // 允许admin wechat模块请求
        if (request_block(['admin', 'wechat'])) {
            return false;
        }

        // 万分之一抛出异常
        if (rand(1, 10000) === 1) {
            abort(502);
        }
    }

    /**
     * 拦截频繁请求
     * 拦截非法请求
     * @access private
     * @param
     * @return void
     */
    private function intercept()
    {
        $ip = request()->ip();
        // 如果缓存不存在,生成缓存
        if (!$data = cache('Concurrent IP' . md5($ip))) {
            cache('Concurrent IP' . md5($ip), [
                'total' => 1,
                'time'  => time()
            ]);
        } else {
            // 缓存存在

            // 更新旧缓存数据
            if (date('Ymd', $data['time']) < date('Ymd')) {
                $data = [
                    'total' => 1,
                    'time'  => time()
                ];
                cache('Concurrent IP' . md5($ip), $data);
            }

            // 判断请求是否在合法范围内
            // 十秒内超过1000次请求抛出异常
            if ($data['time'] + 10 >= time() && $data['total'] >= 1000) {
                // 更新请求时间确保每次进行异常抛出
                cache('Concurrent IP' . md5($ip), [
                    'total' => $data['total'],
                    'time'  => strtotime(date('Y-m-d 23:59:59'))
                ]);

                return true;
            }

            // 更新请求次数与时间
            if ($data['time'] + 10 >= time()) {
                cache('Concurrent IP' . md5($ip), [
                    'total' => ++$data['total'],
                    'time'  => $data['time']
                ]);
            } else {
                cache('Concurrent IP' . md5($ip), [
                    'total' => 1,
                    'time'  => time()
                ]);
            }
        }

        return false;
    }

    public function restart()
    {
        # code...
    }
}
