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
        $json = [];
        $result = false;

        $json['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $json['error_msg'] = 'ILLEGAL';
        } elseif (!$this->hasLogic()) {
            $json['error_msg'] = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $json['error_msg'] = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic  = $this->object;
            $action = $this->action;
            $result = $logic->$action();
            $json['error_msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

            $json['return_code'] = 'ERROR';
            if ($result !== false) {
                $json['return_code']   = 'SUCCESS';
                $json['return_msg']    = '';
                $json['return_result'] = $result;
            }
        }

        $json['use_time_memory'] = use_time_memory();

        remove_old_upload_file(false);

        return json($json)->header([
            'Cache-control' => 'public,max-age=1140',
            'Expires' => gmdate('D, d M Y H:i:s', strtotime('+1 days')) . ' GMT',
        ]);
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        $json = [];
        $result = false;

        $json['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $json['error_msg'] = 'ILLEGAL';
        } elseif (!$this->hasLogic()) {
            $json['error_msg'] = $this->logic . ' undefined';
        } elseif (!$this->hasAction()) {
            $json['error_msg'] = $this->logic . '->' . $this->action . ' undefined';
        } else {
            $logic  = $this->object;
            $action = $this->action;
            $result = $logic->$action();
            $json['error_msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

            $json['return_code'] = 'ERROR';

            if ($result !== false) {
                // 操作返回信息
                if ($result === true) {
                    $json['return_code'] = 'SUCCESS';
                    $json['return_msg']  = lang('save success');
                } else {
                    $json['return_code'] = 'ERROR';
                    $json['return_msg']  = $result;
                }

                $json['return_result'] = '';
            }
        }

        $json['use_time_memory'] = use_time_memory();

        remove_old_upload_file();

        return json($json, 201);
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        $json = [];
        $result = false;

        $json['form data']   = input('param.');

        if (!has_illegal_ajax_sign() || !$this->hasAuth(true)) {
            $json['error_msg'] = 'ILLEGAL';
        } else {
            $result = logic('admin/upload')->file();
            $json['error_msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

            if (is_string($result)) {
                $json['return_code']   = 'ERROR';
                $json['return_msg']    = $result;
                $json['return_result'] = '';
            } else {
                $json['return_code']   = 'SUCCESS';
                $json['return_msg']    = '';
                $json['return_result'] = $result;
            }
        }

        $json['use_time_memory'] = use_time_memory();

        return json($json, 201);
    }
}
