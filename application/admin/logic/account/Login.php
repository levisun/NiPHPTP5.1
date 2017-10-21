<?php
/**
 *
 * 登录 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\account;

class Login
{

    /**
     * 获得用户信息
     * @access public
     * @param  string $_username 用户名
     * @return array OR false
     */
    public function getUserData($_username)
    {
        // 实例化Admin表模型类
        $admin = model('Admin');

        $map = [
            ['a.username', '=', $_username],
        ];
        $result =
        $admin->view('admin a', 'id,username,password,email,salt')
        // 管理员组关系表
        ->view('role_admin ra', 'user_id', 'a.id=ra.user_id')
        // 组表
        ->view('role r', ['id'=>'role_id', 'name'=>'role_name'], 'r.id=ra.role_id')
        ->where($map)
        ->find();

        return !empty($result) ? $result : false;
    }

    /**
     * 验证登录密码
     * @access public
     * @param  string  $_form_pws 请求密码
     * @param  string  $_password 密码
     * @param  string  $_salt     佐料
     * @return boolean
     */
    public function checkPassword($_form_pws, $_password, $_salt)
    {
        $_form_pws = md5(trim($_form_pws));
        $_form_pws = md5($_form_pws . $_salt);

        return $_password === $_form_pws;
    }

    /**
     * 更新登录信息
     * @access public
     * @param  int    $_user_id  管理员ID
     * @param  string $_login_ip 登录IP
     * @return boolean
     */
    public function updateLogin($_user_id, $_login_ip)
    {
        $request_url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $_login_ip;
        $ip = json_decode(file_get_contents($request_url), true);

        $update_data = [
            // 登录IP
            'last_login_ip'      => $_login_ip,
            // 登录IP所在地区
            'last_login_ip_attr' =>
            $ip['data']['region'] . $ip['data']['city'] . '[' . $ip['data']['isp'] . ']',
        ];

        $map = [
            ['id', '=', $_user_id],
        ];

        // 实例化Admin表模型类
        $admin = model('Admin');

        $result =
        $admin->where($map)
        ->update($update_data);

        return $result ? true : false;
    }

    /**
     * 生成登录用户认证信息
     * @access public
     * @param  array  $_user_data 管理员数据
     * @return void
     */
    public function createAuth($_user_data)
    {
        // 删除危险信息
        unset($_user_data['password'], $_user_data['salt'], $_user_data['user_id']);
        // 生成管理员数据
        session('admin_data', $_user_data);
        // 生成认证ID
        session(config('user_auth_key'), $_user_data['id']);
    }

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
        $request_log = model('RequestLog');

        // 日志是否存在
        $map = [
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ];

        $result =
        $request_log->where($map)
        ->value('count');

        if ($result) {
            if ($result >= 3) {
                // 复位
                $data = ['count' => 1];
            } else {
                // 存在增加1
                $data = ['count' => ['exp', 'count+1']];
            }
            $request_log->where($map)
            ->update($data);
        } else {
            // 新建请求错误记录
            $data = [
                'ip'     => $_login_ip,
                'module' => $_module,
                'count'  => 1,
            ];
            $request_log->create($data);
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
        $request_log = model('RequestLog');

        $result =
        $request_log->where($map)
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
        $request_log = model('RequestLog');

        $map = [
            ['ip', '=', $_login_ip],
            ['module', '=', $_module],
        ];
        $request_log->where($map)
        ->delete();

        // 删除过期的日志(保留一个月)
        $map = [
            ['create_time', '<=', strtotime('-30 days')],
        ];
        $request_log->where($map)
        ->delete();
    }
}
