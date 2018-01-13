<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  api\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Receive.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\api\controller;

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
     * 参数请求
     * @access public
     * @param
     * @return json
     */
    public function params()
    {
        $this->getLAL();

        $result = false;

        if (!$this->hasIllegal()) {
            $error = 'illegal';
        } elseif (!$this->hasLogic()) {
            $error = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $error = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic = $this->object;
            $action = $this->action;
            $result = $logic->$action();
        }

        return json([
            'params' => [
                'method' => $this->method,
                'logic'  => $this->logic,
                'action' => $this->action,
                'layer'  => $this->layer,
                'sign'   => $this->sign,
                'post'   => input('post.'),
            ],
            'result'   => $result === false ? [] : $result,
            'errorMsg' => $result === false ? $error : 'success'
        ]);
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
            if ($sign_time + 12 >= time()) {
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
     * 根据method获取logic|action|layer
     * @access private
     * @param
     * @return void
     */
    private function getLAL()
    {
        $this->method = input('param.method');

        $method = explode('.', $this->method);
        $this->logic = $method[0];
        $this->action = !empty($method[1]) ? $method[1] : 'index';
        $this->layer = !empty($method[2]) ? $method[2] : 'logic';
    }
}
