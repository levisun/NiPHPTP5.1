<?php
/**
 *
 * API接口层
 * 权限判断
 *
 * @package   NICMS
 * @category  app\logic\admin\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\logic\admin;

use think\facade\Request;
use think\facade\Response;
use app\logic\Rbac;
use app\model\Action;
use app\model\ActionLog;

class Base
{

    /**
     * 记录操作日志
     * @param  string $_controller
     * @param  string $_action
     * @param  string $_msg
     * @return void
     */
    protected function __actionLog(string $_controller, string $_action, string $_msg = ''): void
    {
        $result =
        Action::where([
            ['name', '=', strtolower($_controller . '_' . $_action)]
        ])
        ->find();

        if ($result) {
            ActionLog::insert([
                'action_id' => $result['id'],
                'user_id'   => session('admin_auth_key'),
                'action_ip' => Request::ip(),
                'module'    => 'admin',
                'remark'    => $_msg,
            ]);
        }


        ActionLog::where([
            ['create_time', '<=', strtotime('-7 days')]
        ])
        ->delete();
    }

    /**
     * 验证权限
     * @access protected
     * @param  App  $app  应用对象
     * @return mexid
     */
    protected function __authenticate(string $_logic, string $_controller, string $_action)
    {
        if (!in_array($_logic, ['account'])) {
            // 用户权限校验
            if (session('?admin_auth_key')) {
                $result =
                (new Rbac)->authenticate(
                    session('admin_auth_key'),
                    'admin',
                    $_logic,
                    $_controller,
                    $_action
                );
            } else {
                $result = false;
            }
        } else {
            $result = true;
        }

        return $result ? false : [
            'debug' => false,
            'cache' => false,
            'msg'   => Lang::get('authenticate error'),
            'data'  => Request::param('', [], 'trim')
        ];;
    }
}
