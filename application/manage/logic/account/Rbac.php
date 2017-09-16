<?php
/**
 *
 * 权限验证 - 帐户 - 业务层
 * 基于角色的数据库方式验证类
 *
 * @package   NiPHPCMS
 * @category  manage\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Rbac.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\logic\account;

class Rbac
{
    private $model;

    private $module;
    private $controller;
    private $action;

    private $user_auth_on;
    private $user_auth_type;

    private $require_auth_module;
    private $not_auth_module;

    private $require_auth_controller;
    private $not_auth_controller;

    private $require_auth_action;
    private $not_auth_action;

    public function __construct()
    {
        $this->model = \think\Loader::model('Node', 'model', false, 'admin');

        $this->module     = strtoupper(request()->module());
        $this->controller = strtoupper(request()->controller());
        $this->action     = strtoupper(request()->action());

        // 是否需要认证
        $this->user_auth_on = config('user_auth_on');
        // 验证类型
        $this->user_auth_type = config('user_auth_type');

        // 需要认证模块
        $this->require_auth_module = strtoupper(config('require_auth_module'));
        // 无需认证模块
        $this->not_auth_module = strtoupper(config('not_auth_module'));

        // 需要认证的控制器
        $this->require_auth_controller = strtoupper(config('require_auth_controller'));
        // 无需认证的控制器
        $this->not_auth_controller = strtoupper(config('not_auth_controller'));

        // 需要认证的方法
        $this->require_auth_action = strtoupper(config('require_auth_action'));
        // 无需认证的方法
        $this->not_auth_action = strtoupper(config('not_auth_action'));
    }

    /**
     * 检查当前操作是否需要认证
     * @access private
     * @param
     * @return boolean
     */
    private function checkAccess()
    {
        if (!$this->user_auth_on) {
            return false;
        }

        $_module = [];
        $_controller = [];
        $_action = [];

        if (!$this->require_auth_module) {
            //需要认证的模块
            $_module['yes'] = explode(',', $this->require_auth_module);
        } else {
            //无需认证的操作
            $_module['no'] = explode(',', $this->not_auth_module);
        }

        if (!$this->require_auth_controller) {
            //需要认证的模块
            $_controller['yes'] = explode(',', $this->require_auth_controller);
        } else {
            //无需认证的操作
            $_controller['no'] = explode(',', $this->not_auth_controller);
        }

        if (!$this->require_auth_action) {
            //需要认证的模块
            $_action['yes'] = explode(',', $this->require_auth_action);
        } else {
            //无需认证的操作
            $_action['no'] = explode(',', $this->not_auth_action);
        }

        if (!empty($_module['no']) && in_array($this->module, $_module['no'])) {
            return false;
        }

        if (!empty($_module['yes']) && in_array($this->module, $_module['yes'])) {
            if (!empty($_controller['no']) && in_array($this->controller, $_controller['no'])) {
                return false;
            }
            if (!empty($_controller['yes']) && in_array($this->controller, $_controller['yes'])) {
                if (!empty($_action['no']) && in_array($this->action, $_action['no'])) {
                    return false;
                }
                if (!empty($_action['yes']) && in_array($this->action, $_action['yes'])) {
                    return true;
                }
            }
        }
    }

    /**
     * 获得当前认证号对应权限
     * @access private
     * @param  int     $auth_id
     * @param  int     $level
     * @param  int     $pid
     * @return array
     */
    private function getAuth($auth_id, $level = 1, $pid = 0)
    {
        $map = [
            'role.status'        => 1,
            'node.status'        => 1,
            'node.level'         => $level,
            'node.pid'           => $pid,
            'role_admin.user_id' => $auth_id
        ];

        $result =
        $this->model->view('node', ['id', 'name'])
        ->view('role_admin', [], 1)
        ->view('role', [], 'role.id=role_admin.role_id')
        ->view('access', [], ['access.role_id=role.id', 'access.node_id=node.id'])
        ->where($map)
        ->select();

        $auth = [];
        foreach ($result as $key => $value) {
            $auth[] = $value->toArray();
        }

        return $auth;
    }

    /**
     * 取得当前认证号的所有权限列表
     * @access private
     * @param  int     $auth_id
     * @return array
     */
    private function getAccessList($auth_id)
    {
        $access = [];

        $module = $this->getAuth($auth_id);

        $controller = $action = [];
        foreach ($module as $m) {
            $controller = $this->getAuth($auth_id, 2, $m['id']);

            foreach ($controller as $c) {
                $action = $this->getAuth($auth_id, 3, $c['id']);
                $_a = [];
                foreach ($action as $a) {
                    $_a[$a['name']] = $a['id'];
                }
                $access[strtoupper($m['name'])][strtoupper($c['name'])] = array_change_key_case($_a, CASE_UPPER);
            }
        }

        return $access;
    }

    /**
     * 权限认证的过滤器方法
     * @access private
     * @param  int     $auth_id
     * @return array
     */
    private function accessDecision($auth_id)
    {
        if ($this->user_auth_type == 2) {
            $_access_list = $this->getAccessList($auth_id);
        } else {
            if (session('?_access_list')) {
                $_access_list = session('_access_list');
            } else {
                session('_access_list', $this->getAccessList($auth_id));
            }
        }

        if (isset($_access_list[$this->module][$this->controller][$this->action])) {
            return true;
        } else {
            return false;
        }
    }

    public function checkAuth($auth_id)
    {
        // 检查当前操作是否需要认证
        if ($this->checkAccess()) {
            return $this->accessDecision($auth_id);
        } else {
            return true;
        }
    }
}
