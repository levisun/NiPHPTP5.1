<?php
/**
 *
 * 请求日志 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: RequestLog.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\RequestLog as ModelRequestLog;

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
        // 实例化请求日志表模型
        $model_request_log = new ModelRequestLog;

        // 日志是否存在
        $map = [
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ];

        $count =
        $model_request_log->where($map)
        ->value('count');

        if (!$count) {
            // 新建请求错误记录
            $data = [
                'ip'     => $_login_ip,
                'module' => $_module,
                'count'  => 1,
            ];
            $model_request_log->create($data);
        } elseif ($count && $count < 3) {
            $data = ['count' => ['exp', 'count+1']];
            $model_request_log->where($map)
            ->update($data);
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
        $map = [
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
            ['count', '>=', 3],
            ['update_time', '>=', strtotime('-3 hours')],
        ];

        // 实例化请求日志表模型
        $model_request_log = new ModelRequestLog;

        $result =
        $model_request_log->where($map)
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
        // 实例化请求日志表模型
        $model_request_log = new ModelRequestLog;

        $map = [
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ];
        $model_request_log->where($map)
        ->delete();

        // 删除过期的日志(保留一个月)
        $map = [
            ['create_time', '<=', strtotime('-30 days')],
        ];
        $model_request_log->where($map)
        ->delete();
    }
}
