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
 * @version   CVS: $Id: Login.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\logic\account;

class Rbac
{
    private $user_auth_on;
    private $require_auth_module = [];
    private $not_auth_module;

    public function __construct()
    {
        $this->module     = strtoupper(request()->module());
        $this->controller = strtoupper(request()->controller());
        $this->action     = strtoupper(request()->action());

        // 是否需要认证
        $this->user_auth_on        = config('user_auth_on');
        // 需要认证模块
        $this->require_auth_module = strtoupper(config('require_auth_module'));
        // 无需认证模块
        $this->not_auth_module     = strtoupper(config('not_auth_module'));

        // 需要认证的控制器
        $this->require_auth_controller = strtoupper(config('require_auth_controller'));
        // 无需认证的控制器
        $this->not_auth_controller = strtoupper(config('not_auth_controller'));

        // 需要认证的方法
        $this->require_auth_action = strtoupper(config('require_auth_action'));
        // 无需认证的方法
        $this->not_auth_action = strtoupper(config('not_auth_action'));

        // USER_AUTH_ON
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE
// NOT_AUTH_MODULE
// USER_AUTH_GATEWAY 认证网关
// RBAC_DB_DSN  数据库连接DSN
// RBAC_ROLE_TABLE 角色表名称
// RBAC_USER_TABLE 用户表名称
// RBAC_ACCESS_TABLE 权限表名称
// RBAC_NODE_TABLE 节点表名称
    }

    public function checkAccess()
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
}
