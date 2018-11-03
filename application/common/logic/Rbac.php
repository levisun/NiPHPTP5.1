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

    private $user_auth_founder = false;                                         // 创始人ID
    private $user_auth_on      = true;                                          // 是否需要认证
    private $user_auth_type    = 2;                                             // 验证类型

    private $req_auth_module;                                                   // 需要认证模块
    private $not_auth_module;                                                   // 无需认证模块

    private $req_auth_controller;                                               // 需要认证的控制器
    private $not_auth_controller;                                               // 无需认证的控制器

    private $req_auth_method;                                                   // 需要认证的方法
    private $not_auth_method;                                                   // 无需认证的方法

    private $req_auth_action;                                                   // 需要认证的操作
    private $not_auth_action;                                                   // 无需认证的操作

    public function __construct()
    {
        // 创始人ID
        if (config('?user_auth_founder')) {
            $this->user_auth_founder = config('user_auth_founder');
        }

        // 是否需要认证
        if (config('?user_auth_on')) {
            $this->user_auth_on = config('user_auth_on');
        }

        // 验证类型
        if (config('?user_auth_type')) {
            $this->user_auth_type = config('user_auth_type');
        }

        // 需要认证模块
        if (config('?req_auth_module')) {
            $this->req_auth_module = explode(',', strtoupper(config('req_auth_module')));
        }

        // 无需认证模块
        if (config('?not_auth_module')) {
            $this->not_auth_module = explode(',', strtoupper(config('not_auth_module')));
        }

        // 需要认证的控制器
        if (config('?req_auth_controller')) {
            $this->req_auth_controller = explode(',', strtoupper(config('req_auth_controller')));
        }

        // 无需认证的控制器
        if (config('?not_auth_controller')) {
            $this->not_auth_controller = explode(',', strtoupper(config('not_auth_controller')));
        }

        // 需要认证的方法
        if (config('?req_auth_method')) {
            $this->req_auth_method = explode(',', strtoupper(config('req_auth_method')));
        }

        // 无需认证的方法
        if (config('?not_auth_method')) {
            $this->not_auth_method = explode(',', strtoupper(config('not_auth_method')));
        }

        // 需要认证的操作
        if (config('?req_auth_action')) {
            $this->req_auth_action = explode(',', strtoupper(config('req_auth_action')));
        }

        // 无需认证的操作
        if (config('?not_auth_action')) {
            $this->not_auth_action = explode(',', strtoupper(config('not_auth_action')));
        }
    }

    /**
     * 审核用户操作权限
     * @access public
     * @param  integer $_auth_id
     * @return boolean
     */
    public function checkAuth($_auth_id, $_module = '', $_controller = '', $_method = '', $_action = 'query')
    {
        $_auth_id         = (float) $_auth_id;
        $this->module     = strtoupper($_module);
        $this->controller = strtoupper($_controller);
        $this->method     = strtoupper($_method);
        $this->action     = strtoupper($_action);

        if (!$_auth_id) {
            return true;
        }

        // 检查当前操作是否需要认证
        if ($this->checkAccess()) {
            // 验证当前操作权限
            return !$this->accessDecision($_auth_id);
        } else {
            return false;
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

        if (!empty($this->req_auth_module)) {
            // 需要认证的模块
            if (in_array($this->module, $this->req_auth_module)) {
                return true;
            }
        } elseif (!empty($this->not_auth_module)) {
            // 无需认证的模块
            if (in_array($this->module, $this->not_auth_module)) {
                return false;
            }
        }

        if (!empty($this->req_auth_controller)) {
            // 需要认证的控制器
            if (in_array($this->controller, $this->req_auth_controller)) {
                return true;
            }
        } elseif (!empty($this->not_auth_controller)) {
            // 无需认证的控制器
            if (in_array($this->controller, $this->not_auth_controller)) {
                return false;
            }
        }

        if (!empty($this->req_auth_method)) {
            // 需要认证的方法
            if (in_array($this->method, $this->req_auth_method)) {
                return true;
            }
        } elseif (!empty($this->not_auth_method)) {
            if (in_array($this->method, $this->not_auth_method)) {
                return false;
            }
        }

        if (!empty($this->req_auth_action)) {
            // 需要认证的操作
            if (in_array($this->action, $this->req_auth_action)) {
                return true;
            }
        } elseif (!empty($this->not_auth_action)) {
            // 无需认证的操作
            if (in_array($this->action, $this->not_auth_action)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 权限认证的过滤器方法
     * @access private
     * @param  integer $_auth_id
     * @return boolean 权限是否存在
     */
    private function accessDecision($_auth_id)
    {
        if ($this->user_auth_type == 2) {
            // 实时校验权限
            session('_access_list', $this->getAccessList($_auth_id));
        } elseif (!session('?_access_list')) {
            session('_access_list', $this->getAccessList($_auth_id));
        }

        $access_list = session('_access_list');

        if (isset($access_list[$this->module][$this->controller][$this->method][$this->action])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取得当前认证号的所有权限列表
     * @access private
     * @param  integer $_auth_id
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

                    // 添加查询操作
                    $a_ = [
                        'QUERY' => true,
                        'FIND'  => true,
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
     * @param  integer $_auth_id
     * @param  integer $_level
     * @param  integer $_pid
     * @return array
     */
    private function getAuth($_auth_id, $_level = 1, $_pid = 0)
    {
        if ($this->user_auth_founder) {
            $result =
            model('common/Node')
            ->field(['id', 'name'])
            ->where([
                ['status', '=', 1],
                ['level', '=', $_level],
                ['pid', '=', $_pid],
            ])
            ->select();
        } else {
            $result =
            model('common/Node')
            ->view('node', ['id', 'name'])
            ->view('role_admin', [], 1)
            ->view('role', [], 'role.id=role_admin.role_id')
            ->view('access', [], ['access.role_id=role.id', 'access.node_id=node.id'])
            ->where([
                ['role.status', '=', 1],
                ['node.status', '=', 1],
                ['node.level', '=', $_level],
                ['node.pid', '=', $_pid],
                ['role_admin.user_id', '=', $_auth_id],
            ])
            ->select();
        }

        return $result ? $result->toArray() : [];
    }
}
