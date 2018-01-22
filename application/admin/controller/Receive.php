<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Receive.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Env;

class Receive extends Controller
{
    private $module;    // 模块
    private $method;    // 接收method值
    private $logic;     // 业务逻辑类名
    private $action;    // 业务类方法
    private $layer;     // 业务类所在层

    private $file;      // 文件路径
    private $sign;      // 加密签名

    private $object;    // 业务逻辑类实例化

    /**
     * 初始化
     * 根据method获取logic|action|layer
     * @access protected
     * @param
     * @return void
     */
    protected function initialize()
    {
        LoadLang();

        $this->method = input('post.method');

        $method = explode('.', $this->method);
        $this->logic  = $method[0];
        $this->action = !empty($method[1]) ? $method[1] : 'index';
        $this->layer  = !empty($method[2]) ? $method[2] : 'logic';
    }

    /**
     * 判断业务类是否存在
     * @access private
     * @param
     * @return boolean
     */
    private function hasIllegal()
    {
        $this->sign = cookie('?__sign') ? cookie('__sign') : false;

        if ($this->sign) {
            list($this->module, $sign_time) = explode('.', decrypt($this->sign), 2);
            if ($sign_time + 60 >= time()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 判断业务类是否存在
     * @access private
     * @param
     * @return boolean
     */
    private function hasLogic()
    {
        $this->file  = Env::get('app_path') . DIRECTORY_SEPARATOR;
        $this->file .= $this->module . DIRECTORY_SEPARATOR;
        $this->file .= 'logic' . DIRECTORY_SEPARATOR;

        if ($this->layer !== 'logic') {
            $this->file .= $this->layer . DIRECTORY_SEPARATOR;
        }

        $this->file .= $this->logic . '.php';

        if (is_file($this->file)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断业务类中方法是否存在
     * @access private
     * @param
     * @return boolean
     */
    private function hasAction()
    {
        $this->object = logic($this->module . '/' . $this->logic, $this->layer);
        if (method_exists($this->object, $this->action)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 参数请求
     * @access public
     * @param
     * @return json
     */
    public function params()
    {
        $result = [];
        $receive = false;

        if (!$this->hasIllegal()) {
            $error = 'ILLEGAL';
        } elseif (!$this->hasLogic()) {
            $error = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $error = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic   = $this->object;
            $action  = $this->action;
            $receive = $logic->$action();
        }

        $result['form data'] = input('param.');
        $result['error_msg'] = $receive === false ? $error : 'SUCCESS';

        if ($receive !== false) {
            $result['return_result'] = $receive;
            $result['return_code']   = !empty($receive['return_code']) ? $receive['return_code'] : 'SUCCESS';
        } else {
            $result['return_code'] = 'ERROR';
        }

        return json($result);
    }
}
