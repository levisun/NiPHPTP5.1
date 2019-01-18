<?php
/**
 *
 * 公共函数文件
 *
 * @package   NiPHP
 * @category  application\admin
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */

/**
 * 自动添加HTML文档中的meta等信息
 * @param  string $_content
 * @return string
 */
function replace_meta($_content)
{
    // 网站标题与面包屑
    $tit_bre = logic('admin/account/auth')->getTitBre();
    return html_head_foot($tit_bre, $_content);
}

/**
 * 节点格式化
 * @param  array $_result
 * @param  int   $_pid
 * @return array
 */
function node_format($_result, $_pid = 0)
{
    $node = [];
    foreach ($_result as $key => $value) {
        if ($value['pid'] == $_pid) {
            $ext = '';
            for ($i=1; $i < $value['level']; $i++) {
                $ext .= '|__';
            }
            $value['title'] = $ext . $value['title'];

            $node[] = $value;

            $temp = node_format($_result, $value['id']);
            if (!empty($temp)) {
                $node = array_merge($node, $temp);
            }

            unset($_result[$key]);
        }
    }

    return $node;
}

/**
 * 删除旧的未保存的上传文件
 * @param  mixed $_path
 * @return void
 */


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

    // 删除过期日志
    $days = APP_DEBUG ? '-7 days' : '-90 days';
    $map = [
        ['create_time', '<=', strtotime($days)]
    ];
    model('common/actionLog')
    ->where($map)
    ->delete();
}
