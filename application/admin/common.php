<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHPCMS
 * @category  application\admin
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 记录操作日志
 * @access public
 * @param  string $_msg
 * @param  string $_action
 * @return void
 */
function create_action_log($_msg, $_action = '')
{
    if (!$_action) {
        $_action = strtolower(request()->controller() . '_' . request()->action());
    }

    $map = [
        ['name', '=', $_action]
    ];
    $result = model('common/action')
    ->where($map)
    ->find();

    $data = [
        'action_id' => $result['id'],
        'user_id'   => session(config('user_auth_key')),
        'action_ip' => request()->ip(0, true),
        'module'    => request()->module(),
        'remark'    => $_msg,
    ];
    model('common/actionLog')
    ->added($data);
}
