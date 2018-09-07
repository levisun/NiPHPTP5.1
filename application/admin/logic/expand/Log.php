<?php
/**
 *
 * 系统日志 - 扩展 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\expand
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\expand;

class Log
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        // 删除过期的日志(保留三个月)
        model('common/ActionLog')
        ->where([
            ['create_time', '<=', strtotime('-90 days')]
        ])
        ->delete();

        $result =
        model('common/ActionLog')
        ->view('action_log l', 'action_ip,module,remark,create_time')
        ->view('action a', 'title', 'a.id=l.action_id')
        ->view('admin u', 'username', 'u.id=l.user_id')
        ->view('role_admin ra', 'user_id', 'ra.user_id=l.user_id')
        ->view('role r', ['name'=>'role_name'], 'r.id=ra.role_id')
        ->order('l.create_time DESC')
        ->paginate(null, null, [
            'path' => url('expand/log'),
        ]);

        $page = $result->render();
        $list = $result->toArray();

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $page
        ];
    }
}
