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

class Api
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
    public function __construct()
    {
        $this->module = strtolower(request()->module());

        $this->method = input('post.method');

        $count = count(explode('.', $this->method));

        if ($count == 3) {
            list($this->layer, $this->logic, $this->action) =
            explode('.', $this->method, 3);
        } elseif ($count == 2) {
            list($this->logic, $this->action) =
            explode('.', $this->method, 2);
            $this->layer = 'logic';
        } elseif ($count == 1) {
            list($this->logic) =
            explode('.', $this->method, 1);
            $this->layer  = 'logic';
            $this->action = 'index';
        } else {
            abort(400, '错误请求,非法参数!');
        }

        // 加载语言包
        lang(':load');
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
        $this->object = logic($this->module . '/' . $this->layer . '/' . $this->logic);
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
     * 返回错误信息
     * @param  string  $_msg         提示信息
     * @param  integer $_code        错误代码
     * @param  array   $_data        返回数据
     * @param  array   $_extend_data 附加数据
     * @return json
     */
    private function outputData($_msg = '', $_code = 'SUCCESS', $_data, $_extend_data = [])
    {
        $return = [
            'code' => $_code,
            'msg'  => $_msg,
            'data' => $_data,
            'oth'  => $_extend_data,
        ];

        return json($return);
    }

    /**
     * 返回错误信息
     * @param  string  $_msg         错误信息
     * @param  integer $_code        错误代码
     * @param  array   $_extend_data 附加数据
     * @return json
     */
    private function outputError($_msg = 'ERROR', $_code = 40001, $_extend_data = [])
    {
        $return = [
            'code' => $_code,
            'msg'  => $_msg,
            'oth'  => $_extend_data,
        ];

        return json($return);
    }

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $output = $this->outputError(
                'ILLEGAL',
                40001,
                input('param.')
            );
        } elseif (!$this->hasLogic()) {
            $output = $this->outputError(
                $this->logic . ' undefined',
                40002,
                input('param.')
            );
        } elseif (!$this->hasAction()) {
            $output = $this->outputError(
                $this->logic . '->' . $this->action . ' undefined',
                40003,
                input('param.')
            );
        } else {
            $logic  = $this->object;
            $action = $this->action;
            $result = $logic->$action();

            remove_old_upload_file(false);

            if ($result === false) {
                $output = $this->outputError(
                    'request error',
                    40004,
                    input('param.')
                );
            } else {
                $output = $this->outputData(
                    'request success',
                    'SUCCESS',
                    $result,
                    input('param.')
                );
            }
        }

        return $output;
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        if (!has_illegal_ajax_sign() || !$this->hasAuth()) {
            $output = $this->outputError(
                'ILLEGAL',
                40001,
                input('param.')
            );
        } elseif (!$this->hasLogic()) {
            $output = $this->outputError(
                $this->logic . ' undefined',
                40002,
                input('param.')
            );
        } elseif (!$this->hasAction()) {
            $output = $this->outputError(
                $this->logic . '->' . $this->action . ' undefined',
                40003,
                input('param.')
            );
        } else {
            $logic  = $this->object;
            $action = $this->action;
            $result = $logic->$action();

            remove_old_upload_file();

            if ($result === false) {
                $output = $this->outputError(
                    'data error',
                    40004,
                    input('param.')
                );
            } else {
                if ($result === true) {
                    $output = $this->outputData(
                        lang('save success'),
                        'SUCCESS',
                        $result,
                        input('param.')
                    );
                } else {
                    $output = $this->outputData(
                        $result,
                        'ERROR',
                        [],
                        input('param.')
                    );
                }
            }
        }

        return $output;
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
            $output = $this->outputError(
                'ILLEGAL',
                40001,
                input('param.')
            );
        } else {
            $result = logic('admin/upload')->file();
            $json['msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

            if (is_string($result)) {
                $json['code']   = 'ERROR';
                $json['msg']    = $result;
                $json['result'] = '';
            } else {
                $json['code']   = 'SUCCESS';
                $json['msg']    = '';
                $json['result'] = $result;
            }
        }

        return json($json);
    }
}
