<?php
/**
 *
 * 请求日志 - 业务层
 *
 * @package   NiPHP
 * @category  application\common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\logic;

class RequestLog
{

    /**
     * 请求错误锁定IP
     * @access public
     * @param  string $_login_ip 登录IP
     * @param  string $_module   模块
     * @return boolean
     */
    public function lockIp($_login_ip, $_module)
    {
        // 日志是否存在
        $count =
        model('common/RequestLog')
        ->where([
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ])
        ->value('count');

        if (!$count) {
            // 新建请求错误记录
            model('common/RequestLog')
            ->create([
                'ip'     => $_login_ip,
                'module' => $_module,
                'count'  => 1,
            ]);
        } elseif ($count && $count < 3) {
            model('common/RequestLog')
            ->where([
                ['ip', '=', $_login_ip],
                ['module', '=', $_module],
            ])
            ->update([
                'count' => ['exp', 'count+1']
            ]);
        }
    }

    /**
     * 审核IP地址错误请求超过三次
     * @access public
     * @param  string $_login_ip 登录IP
     * @param  string $_module   模块
     * @return boolean
     */
    public function isLockIp($_login_ip, $_module)
    {
        // 三小时内错误请求超过三次
        $result =
        model('common/RequestLog')
        ->where([
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
            ['count', '>=', 3],
            ['update_time', '>=', strtotime('-3 hours')],
        ])
        ->value('count');

        return $result ? true : false;
    }

    /**
     * 登录成功清除请求错误日志
     * @access public
     * @param  string $_login_ip 登录IP
     * @param  string $_module   模块
     * @return void
     */
    public function removeLockIp($_login_ip, $_module)
    {
        model('common/RequestLog')
        ->where([
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ])
        ->delete();

        // 删除过期的日志(保留一个月)
        model('common/RequestLog')
        ->where([
            ['create_time', '<=', strtotime('-30 days')],
        ])
        ->delete();
    }
}
