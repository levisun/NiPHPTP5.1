<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use think\Controller;

class Api extends Controller
{
    private $module;    // 模块
    private $method;    // 接收method值
    private $logic;     // 业务逻辑类名
    private $action;    // 业务类方法
    private $layer;     // 业务类所在层

    private $file_path; // 文件路径
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
        // 加载语言包
        lang(':load');

        $this->module = strtolower($this->request->module());

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
    private function hasLogic()
    {
        $this->file_path  = env('app_path') . DIRECTORY_SEPARATOR;
        $this->file_path .= $this->module . DIRECTORY_SEPARATOR;
        $this->file_path .= 'logic' . DIRECTORY_SEPARATOR;

        if ($this->layer !== 'logic') {
            $this->file_path .= $this->layer . DIRECTORY_SEPARATOR;
        }

        $this->file_path .= $this->logic . '.php';

        if (is_file($this->file_path)) {
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
     * 权限验证
     * @access private
     * @param  boolean $_strict 是否严格校验
     * @return boolean
     */
    private function hasAuth($_strict = false)
    {
        $not_auth_action = explode(',', config('not_auth_action'));

        if (session('?' . config('user_auth_key'))) {
            return true;
        } elseif (!$_strict && in_array($this->action, $not_auth_action)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        $result = [];
        $receive = false;

        $result['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $result['error_msg'] = 'ILLEGAL';
        } elseif (!$this->hasLogic()) {
            $result['error_msg'] = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $result['error_msg'] = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic   = $this->object;
            $action  = $this->action;
            $receive = $logic->$action();
            $result['error_msg']   = $receive === false ? 'EMPTY' : 'SUCCESS';

            $result['return_code'] = 'ERROR';
            if ($receive !== false) {
                $result['return_code']   = 'SUCCESS';
                $result['return_msg']    = '';
                $result['return_result'] = $receive;
            }
        }

        $result['use_time_memory'] = use_time_memory();

        remove_old_upload_file(false);

        return json($result);
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        $result = [];
        $receive = false;

        $result['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $result['error_msg'] = 'ILLEGAL';
        } elseif (!$this->hasLogic()) {
            $result['error_msg'] = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $result['error_msg'] = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic   = $this->object;
            $action  = $this->action;
            $receive = $logic->$action();
            $result['error_msg']   = $receive === false ? 'EMPTY' : 'SUCCESS';

            $result['return_code'] = 'ERROR';

            if ($receive !== false) {
                // 操作返回信息
                if ($receive === true) {
                    $result['return_code'] = 'SUCCESS';
                    $result['return_msg']  = lang('save success');
                } else {
                    $result['return_code'] = 'ERROR';
                    $result['return_msg']  = $receive;
                }

                $result['return_result'] = '';
            }
        }

        $result['use_time_memory'] = use_time_memory();

        remove_old_upload_file();

        return json($result);
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        $result = [];
        $receive = false;

        $result['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth(true)) {
            $result['error_msg'] = 'ILLEGAL';
        } else {
            $receive = logic('admin/upload')->file();
            $result['error_msg']   = $receive === false ? 'EMPTY' : 'SUCCESS';

            if (is_string($receive)) {
                $result['return_code']   = 'ERROR';
                $result['return_msg']    = $receive;
                $result['return_result'] = '';
            } else {
                $result['return_code']   = 'SUCCESS';
                $result['return_msg']    = '';
                $result['return_result'] = $receive;
            }
        }

        $result['use_time_memory'] = use_time_memory();

        return json($result);
    }
}
