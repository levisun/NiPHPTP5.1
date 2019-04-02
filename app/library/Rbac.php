<?php
/**
 *
 * 服务层
 * 权限校验类
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use app\library\Base64;
use app\model\Node as ModelNode;

class Rbac
{
    private $params = [
        'auth_founder'        => 1,                                             // 超级管理员ID
        'auth_type'           => 1,                                             // 验证方式
        'auth_on'             => false,                                         // 是否校验
        'not_auth_app'        => [],
        'not_auth_controller' => [],
        'not_auth_action'     => [
            'query',
            'find',
            'all',
            'get',
        ],
    ];

    /**
     * 审核用户操作权限
     * @access public
     * @param  int    $_userid     用户ID
     * @param  string $_app        应用名
     * @param  string $_logic      业务层名
     * @param  string $_controller 控制器名
     * @param  string $_action     方法名
     * @return boolean
     */
    public function authenticate(int $_userid, string $_app, string $_logic, string $_controller, string $_action): bool
    {
        $this->params = array_merge($this->params, Config::get('rbac_auth'));

        if ($this->checkAccess($_app, $_logic, $_controller, $_action)) {
            // 实时检验权限
            if ($this->params['auth_type'] == 1) {
                $__authenticate_list = $this->accessDecision($_userid);
            }
            // 非实时校验
            // 权限写入session
            else {
                if (Session::has('__authenticate_list')) {
                    $__authenticate_list = Session::get('__authenticate_list');
                    $__authenticate_list = Base64::decrypt($__authenticate_list);
                } else {
                    $__authenticate_list = $this->accessDecision($_userid);
                    Session::set('__authenticate_list', Base64::encrypt($__authenticate_list));
                }
            }

            return isset($__authenticate_list[$_app][$_logic][$_controller][$_action]);
        } else {
            return false;
        }
    }

    /**
     * 检查当前操作是否需要认证
     * @access private
     * @param  string $_app        应用名
     * @param  string $_logic      业务层名
     * @param  string $_controller 控制器名
     * @param  string $_action     方法名
     * @return boolean
     */
    private  function checkAccess(string $_app, string $_logic, string $_controller, string $_action): bool
    {
        if ($this->params['auth_on'] !== true) {
            return false;
        }

        if (!empty($this->params['not_auth_app'])) {
            $this->params['not_auth_app'] = array_map('strtolower', $this->params['not_auth_app']);
            if (in_array($_app, $this->params['not_auth_app'])) {
                return false;
            }
        }

        if (!empty($this->params['not_auth_controller'])) {
            $this->params['not_auth_controller'] = array_map('strtolower', $this->params['not_auth_controller']);
            if (in_array($_controller, $this->params['not_auth_controller'])) {
                return false;
            }
        }

        if (!empty($this->params['not_auth_action'])) {
            $this->params['not_auth_action'] = array_map('strtolower', $this->params['not_auth_action']);
            if (in_array($_action, $this->params['not_auth_action'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * 检查当前操作是否需要认证
     * @access private
     * @param  int    $_userid     用户ID
     * @return array
     */
    private function accessDecision(int $_userid): array
    {
        $access = [];

        $app = $this->getNode($_userid);
        foreach ($app as $a) {
            $a['name'] = strtolower($a['name']);
            $logic = $this->getNode($_userid, 2, $a['id']);

            foreach ($logic as $l) {
                $l['name'] = strtolower($l['name']);
                $controller = $this->getNode($_userid, 3, $l['id']);

                foreach ($controller as $c) {
                    $c['name'] = strtolower($c['name']);
                    $action = $this->getNode($_userid, 4, $c['id']);

                    $access[$a['name']][$l['name']][$c['name']]['query'] = true;
                    // $access[$a['name']][$l['name']][$c['name']]['find'] = true;

                    foreach ($action as $act) {
                        $access[$a['name']][$l['name']][$c['name']][$act['name']] = true;
                    }
                }
            }
        }

        return $access;
    }

    /**
     * 获得当前认证号对应权限
     * @access private
     * @param  int $_userid
     * @param  int $_level
     * @param  int $_pid
     * @return array
     */
    private function getNode(int $_userid, int $_level = 1, int $_pid = 0): array
    {
        if ($this->params['auth_founder'] == $_userid) {
            $result =
            ModelNode::field(['id', 'name'])
            ->where([
                ['status', '=', 1],
                ['level', '=', $_level],
                ['pid', '=', $_pid],
            ])
            ->cache(__METHOD__ . 'founder' . $_userid . $_level . $_pid, 1200)
            ->select()
            ->toArray();
        } else {
            $result =
            ModelNode::view('node', ['id', 'name'])
            ->view('role_admin', [], '(role_admin.user_id=' . $_userid . ')')
            ->view('role', [], '(role.status=1 AND role.id=role_admin.role_id)')
            ->view('access', [], '(access.role_id=role.id AND access.node_id=node.id)')
            ->where([
                ['node.status', '=', 1],
                ['node.level', '=', $_level],
                ['node.pid', '=', $_pid],
            ])
            ->cache(__METHOD__ . $_userid . $_level . $_pid, 1200)
            ->select()
            ->toArray();
        }

        return $result;
    }
}
