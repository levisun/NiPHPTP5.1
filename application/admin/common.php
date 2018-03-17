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
 * 删除旧的未保存的上传文件
 * @param  mixed $_path
 * @return void
 */
function remove_old_upload_file($_path = null)
{
    if (cookie('?__upfile')) {
        if (is_null($_path)) {
            // $_path=null时表示上传文件已写入库中，删除此记录
            cookie('__upfile', null);
        } else {
            // 删除上次上传没有保存文件
            $upfile = cookie('__upfile');
            if (!empty($upfile)) {
                \File::remove(env('root_path') . basename(request()->root()) . $upfile);
            }
            cookie('__upfile', $_path);
        }
    } else {
        cookie('__upfile', $_path);
    }
}

/**
 * 记录操作日志
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
