<?php
/**
 *
 * 登录 - 帐户 - 业务层
 *
 * @package   NiPHPCMS
 * @category  manage\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\logic\account;

class Login
{

    /**
     * 获得用户信息
     * @access public
     * @param
     * @return array OR false
     */
    public function getUserData($username)
    {
        $admin = model('Admin');

        $map = ['a.username' => $username];
        $result =
        $admin->view('admin a', 'id,username,password,email,salt')
        ->view('role_admin ra', 'user_id', 'a.id=ra.user_id')
        ->view('role r', ['id'=>'role_id', 'name'=>'role_name'], 'r.id=ra.role_id')
        ->where($map)
        ->find();

        return !empty($result) ? $result->toArray() : false;
    }

    /**
     * 验证登录密码
     * @access public
     * @param
     * @return boolean
     */
    public function checkPassword($form_pws, $password, $salt)
    {
        $form_pws = md5($form_pws . $salt);

        return $password === $form_pws;
    }

    /**
     * 更新登录信息
     * @access public
     * @param
     * @return boolean
     */
    public function updateLogin($user_id, $login_ip)
    {
        $request_url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $login_ip;
        $result = json_decode(file_get_contents($request_url), true);

        $province = $result['data']['region'];
        $city     = $result['data']['city'];
        $isp      = $result['data']['isp'];

        $update_data = [
            'last_login_ip'      => $login_ip,
            'last_login_ip_attr' => $province . $city . '[' . $isp . ']',
        ];

        $map = ['id' => $user_id];

        $admin = model('Admin');

        $result =
        $admin->allowField(true)
        ->isUpdate(true)
        ->save($update_data, $map);

        return $result ? true : false;
    }

    /**
     * 生成登录用户认证信息
     * @access public
     * @param
     * @return void
     */
    public function createAuth($user_data)
    {
        session('ADMIN_DATA', $user_data);
        session(Config('USER_AUTH_KEY'), $user_data['id']);
    }

    /**
     * 请求错误锁定IP
     * @access public
     * @param
     * @return boolean
     */
    public function lockIp($login_ip, $module)
    {
        $request_log = model('RequestLog');

        // 错误请求超过三次锁定IP
        $map = [
            'ip'     => $login_ip,
            'module' => $module,
            ['count', 'EGT', 3],
            ['update_time', 'EGT', strtotime('-3 hours')],
        ];

        $result =
        $request_log->where($map)
        ->value('count');

        if ($result) {
            return true;
        }

        // 日志是否存在
        $map = [
            'ip'     => $login_ip,
            'module' => $module,
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
                $data = [
                    ['count', 'exp', 'count+1']
                ];
            }
            $request_log->allowField(true)
            ->isUpdate(true)
            ->save($data, $map);
        } else {
            // 新建请求错误记录
            $data = [
                'ip'     => $login_ip,
                'module' => $module,
                'count'  => 1,
            ];
            $request_log->data($data)
            ->allowField(true)
            ->isUpdate(false)
            ->save();
        }

        return false;
    }

    /**
     * 登录成功清除请求错误日志
     * @access public
     * @param
     * @return void
     */
    public function removeLockIp($login_ip, $module)
    {
        $request_log = model('RequestLog');

        // 删除过期的日志(保留一个月)
        $map = [
            ['create_time', 'ELT', strtotime('-30 days')]
        ];
        $request_log->where($map)
        ->delete();

        $map = [
            'ip'     => $login_ip,
            'module' => $module,
        ];
        $request_log->where($map)
        ->delete();
    }
}
