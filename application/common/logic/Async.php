<?php
/**
 *
 * 异步请求实现 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\logic;

class Async
{
    private $module = 'common';    // 模块
    private $layer  = 'logic';     // 业务类所在层
    private $class  = 'index';     // 业务逻辑类名
    private $action = 'index';     // 业务类方法

    private $method;    // 接收method值



    private $file_path; // 文件路径
    private $sign;      // 加密签名

    private $object;    // 业务逻辑类实例化

    function __construct()
    {
        lang(':load');
    }

    /**
     * 执行
     * @access public
     * @param
     * @return mixed [boolean|json|array]
     */
    public function exec()
    {
        $object = $this->object;
        $action = $this->action;

        return $object->$action();
    }

    /**
     * 解析请求参数
     * 校验参数合法性
     * @access public
     * @param
     * @return void
     */
    public function analysis()
    {
        $this->module = strtolower(request()->module());

        if (request()->isPost()) {
            $this->method = input('post.method');
        } else {
            $this->method = input('get.method');
        }

        $count = count(explode('.', $this->method));

        if ($count == 3) {
            list($this->layer, $this->class, $this->action) =
            explode('.', $this->method, 3);
        } elseif ($count == 2) {
            list($this->class, $this->action) =
            explode('.', $this->method, 2);
        } elseif ($count == 1) {
            list($this->class) =
            explode('.', $this->method, 1);
        } else {
            return $this->outputError(
                'PARAMETER ERROR',
                'ERROR'
            );
        }

        // 检查文件是否存在
        $this->file_path  = env('app_path');
        $this->file_path .= $this->module . DIRECTORY_SEPARATOR;
        $this->file_path .= 'logic' . DIRECTORY_SEPARATOR;

        if ($this->layer !== 'logic') {
            $this->file_path .= $this->layer . DIRECTORY_SEPARATOR;
        }

        $this->file_path .= $this->class . '.php';
        if (!is_file($this->file_path)) {
            return $this->outputError(
                'METHOD NOT FOUND',
                'ERROR'
            );
        }

        // 检查方法是否存在
        $this->object = logic($this->module . '/' . $this->layer . '/' . $this->class);
        if (!method_exists($this->object, $this->action)) {
            return $this->outputError(
                'ACTION UNDEFINED',
                'ERROR'
            );
        }

        return true;
    }

    /**
     * 返回信息
     * @access public
     * @param  string  $_msg         提示信息
     * @param  integer $_code        代码
     * @param  array   $_data        返回数据
     * @param  array   $_extend_data 附加数据
     * @return json
     */
    public function outputData($_msg = '', $_code = 'SUCCESS', $_data, $_extend_data = [])
    {
        $return = [
            'code' => $_code,
            'msg'  => $_msg,
            'data' => $_data,
            'oth'  => !empty($_extend_data) ? $_extend_data : input('param.'),
        ];

        return json($return);
    }

    /**
     * 返回错误信息
     * @access public
     * @param  string  $_msg         错误信息
     * @param  integer $_code        错误代码
     * @param  array   $_extend_data 附加数据
     * @return json
     */
    public function outputError($_msg = 'ERROR', $_code = 'ERROR', $_extend_data = [])
    {
        $return = [
            'code' => $_code,
            'msg'  => $_msg,
            'oth'  => !empty($_extend_data) ? $_extend_data : input('param.'),
        ];

        return json($return);
    }

    /**
     * 验证异步加密签名
     * @access private
     * @param
     * @return void
     */
    private function verifySign()
    {
        if (cookie('?__a-c')) {
            $http_referer = sha1(
                md5(date('Y-m-d')) .
                md5(request()->domain() .
                    request()->server('http_referer'))
            );

            return cookie('__a-c') === $http_referer;
        } else {
            return false;
        }
    }

    /**
     * 生成异步加密签名
     * @access public
     * @param
     * @return void
     */
    public function createSign()
    {
        // 伪装签名
        cookie('__sign', md5(
            md5(date('Y-m-d')) .
            time() .
            'sign'
        ));

        // 真正签名
        cookie('__a-c', sha1(
            md5(date('Y-m-d')) .
            md5(request()->domain() .
                request()->url(true))
        ));
    }
}
