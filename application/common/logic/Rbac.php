<?php
/**
 *
 * 帐户权限验证 - 业务层
 * 基于角色的数据库方式验证类
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\common\logic;

class Rbac
{
    private $module;        // 模块名
    private $controller;    // 控制器名[分层名]
    private $method;        // 类名
    private $action;        // 方法名

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
     * 审核用户操作权限
     * @access public
     * @param  int     $_auth_id
     * @return boolean
     */
    public function checkAuth($_auth_id, $_module = '', $_controller = '', $_method = '', $_action = 'query')
    {
        $this->module     = strtoupper($_module);
        $this->controller = strtoupper($_controller);
        $this->method     = strtoupper($_method);
        $this->action     = strtoupper($_action);

        // 检查当前操作是否需要认证
        if ($this->checkAccess()) {
            return $this->accessDecision($_auth_id);
        } else {
            return true;
        }
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

        $module_     = [];
        $controller_ = [];
        $method_     = [];

        if ($this->require_auth_module) {
            //需要认证的模块
            $module_['yes'] = explode(',', $this->require_auth_module);
        } else {
            //无需认证的操作
            $module_['no'] = explode(',', $this->not_auth_module);
        }

        if ($this->require_auth_controller) {
            //需要认证的模块
            $controller_['yes'] = explode(',', $this->require_auth_controller);
        } else {
            //无需认证的操作
            $controller_['no'] = explode(',', $this->not_auth_controller);
        }

        if ($this->require_auth_action) {
            //需要认证的模块
            $method_['yes'] = explode(',', $this->require_auth_action);
        } else {
            //无需认证的操作
            $method_['no'] = explode(',', $this->not_auth_action);
        }

        if (!empty($module_['no']) && in_array($this->module, $module_['no'])) {
            if (!empty($controller_['no']) && in_array($this->controller, $controller_['no'])) {
                if (!empty($method_['no']) && in_array($this->method, $method_['no'])) {
                    return false;
                }
            }
        }

        if (!empty($module_['yes']) && in_array($this->module, $module_['yes'])) {
            if (!empty($controller_['yes']) && in_array($this->controller, $controller_['yes'])) {
                if (!empty($method_['yes']) && in_array($this->method, $method_['yes'])) {
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * 权限认证的过滤器方法
     * @access private
     * @param  int     $_auth_id
     * @return array
     */
    private function accessDecision($_auth_id)
    {
        if ($this->user_auth_type == 2) {
            // 实时校验权限
            $access_list = $this->getAccessList($_auth_id);
            session('_access_list', $access_list);
        } else {
            if (session('?_access_list')) {
                $access_list = session('_access_list');
            } else {
                session('_access_list', $this->getAccessList($_auth_id));
                $access_list = session('_access_list');
            }
        }

        if (isset($access_list[$this->module][$this->controller][$this->method][$this->action])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取得当前认证号的所有权限列表
     * @access private
     * @param  int     $_auth_id
     * @return array
     */
    private function getAccessList($_auth_id)
    {
        $access = [];

        $modules = $this->getAuth($_auth_id);

        foreach ($modules as $mod) {
            $mod['name'] = strtoupper($mod['name']);
            $controller = $this->getAuth($_auth_id, 2, $mod['id']);

            foreach ($controller as $con) {
                $con['name'] = strtoupper($con['name']);
                $method = $this->getAuth($_auth_id, 3, $con['id']);

                foreach ($method as $met) {
                    $met['name'] = strtoupper($met['name']);
                    $action = $this->getAuth($_auth_id, 4, $met['id']);

                    $a_ = [
                        'query' => true,
                        'find'  => true,
                    ];
                    foreach ($action as $act) {
                        $a_[$act['name']] = true;
                    }
                    $access[$mod['name']][$con['name']][$met['name']] = array_change_key_case($a_, CASE_UPPER);
                }
            }
        }

        return $access;
    }

    /**
     * 获得当前认证号对应权限
     * @access private
     * @param  int     $_auth_id
     * @param  int     $_level
     * @param  int     $_pid
     * @return array
     */
    private function getAuth($_auth_id, $_level = 1, $_pid = 0)
    {
        $result =
        model('common/Node')
        ->view('node', ['id', 'name'])
        ->view('role_admin', [], 1)
        ->view('role', [], 'role.id=role_admin.role_id')
        ->view('access', [], ['access.role_id=role.id', 'access.node_id=node.id'])
        ->where( [
            ['role.status', '=', 1],
            ['node.status', '=', 1],
            ['node.level', '=', $_level],
            ['node.pid', '=', $_pid],
            ['role_admin.user_id', '=', $_auth_id],
        ])
        ->select();

        return $result ? $result->toArray() : [];
    }
}
