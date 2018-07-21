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
 * @since     2018/7
 */
namespace app\common\logic;

class Async
{
    protected $appkey;              //
    protected $token;               //
    protected $sign;                // 签名
    protected $timestamp;           // 请求时间
    protected $format = 'json';     // 返回数据类型[json|pjson|xml]

    protected $method;              //
    protected $params =[];          // 请求参数

    protected $object;              // 实例化的方法
    protected $module;              // 模块
    protected $layer  = 'logic';    // 业务类所在层
    protected $class  = 'index';    // 业务逻辑类名
    protected $action = 'index';    // 业务类方法

    protected $apiDebug = false;    // 调试信息

    public function __construct()
    {
        // 验证请求方式
        if (!request()->isGet() && !request()->isPost()) {
            $this->outputError('request mode error');
        }

        // 公共参数赋值
        $this->appkey    = input('param.appkey');
        $this->token     = input('param.token');
        $this->sign      = input('param.sign');
        $this->timestamp = input('param.timestamp');
        $this->method    = input('param.method');
        $this->format    = input('param.format');

        // 获取外部参数
        $this->params = input('param.', 'trim');

        // 调试
        $this->apiDebug = APP_DEBUG;
    }

    /**
     * 执行
     * @access protected
     * @param
     * @return mixed
     */
    protected function exec()
    {
        $object = $this->object;
        $action = $this->action;

        return $object->$action();
    }

    /**
     * 初始化
     * @access protected
     * @param
     * @return mixed
     */
    protected function init()
    {
        // 验证参数
        if (!$this->method) {
            return $this->outputError('[METHOD] parameter error');
        }

        // 验证请求来源
        $request = $this->checkRequest();
        if ($request !== true) {
            return $this->outputError($request);
        }

        // 验证Token
        $token = $this->checkToken();
        if ($token !== true) {
            return $this->outputError($token);
        }

        // 验证Sign
        $sign = $this->checkSign();
        if ($sign !== true) {
            return $this->outputError($sign);
        }

        // 查找模型方法
        $method = $this->autoFindMethod();
        if ($method !== true) {
            return $this->outputError($method);
        }

        // 验证Auth
        $auth = $this->checkAuth();
        if ($auth !== true) {
            return $this->outputError($auth);
        }

        return true;
    }

    /**
     * 根据method命名规范查找模型方法
     * @access private
     * @param
     * @return mixed
     */
    private function autoFindMethod()
    {
        $this->module = strtolower(request()->module());

        $count = count(explode('.', $this->method));
        if ($count == 3) {
            list($this->layer, $this->class, $this->action) = explode('.', $this->method, 3);
        } elseif ($count == 2) {
            list($this->class, $this->action) = explode('.', $this->method, 2);
        } elseif ($count == 1) {
            list($this->class) = explode('.', $this->method, 1);
        }

        // 检查文件是否存在
        $file_path  = env('app_path');
        $file_path .= $this->module . DIRECTORY_SEPARATOR;
        $file_path .= 'logic' . DIRECTORY_SEPARATOR;

        if ($this->layer !== 'logic') {
            $file_path .= $this->layer . DIRECTORY_SEPARATOR;
        }

        $file_path .= $this->class . '.php';
        if (!is_file($file_path)) {
            return $this->class . ' model doesn\'t exist';
        }

        // 检查方法是否存在
        $this->object  = logic($this->module . '/' . $this->layer . '/' . $this->class);

        if (method_exists($this->object, $this->action)) {
            return true;
        } else {
            return $class . '->' . $action . ' method doesn\'t exist';
        }
    }

    /**
     * 验证Token是否合法
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkToken()
    {
        if ($this->token) {
            # TODO 查询
        }

        return true;
    }

    /**
     * 验证Auth
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkAuth()
    {
        return false;
    }

    /**
     * 验证异步加密签名
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkSign()
    {
        $params = $this->params;
        ksort($params);

        $string_to_be_signed = '';
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $string_to_be_signed .= $key . '=' . $value . '&';
            }
        }
        $string_to_be_signed = trim($string_to_be_signed, '&');

        if (hash_equals(md5($string_to_be_signed), $this->sign)) {
            return true;
        } else {
            return 'sign error';
        }
    }

    /**
     * 生成请求来源
     * @access public
     * @param
     * @return void
     */
    public function createRequest()
    {
        $http_referer = md5(
            env('root_path') .
            request()->url(true) .
            date('Ymd')
        );
        session('_cr', $http_referer);
    }

    /**
     * 验证请求来源是否合法
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkRequest()
    {
        if (session('?_cr')) {
            $http_referer = md5(
                env('root_path') .
                request()->server('http_referer') .
                date('Ymd')
            );
            if (session('_cr') !== $http_referer) {
                return 'request error';
            }
        } else {
            return 'request error';
        }

        return true;
    }

    /**
     * 返回信息
     * @access public
     * @param  string  $_msg         提示信息
     * @param  integer $_code        代码
     * @param  array   $_data        返回数据
     * @return json
     */
    public function outputData($_msg, $_data, $_code = 'SUCCESS')
    {
        $data = [
            'code' => $_code,
            'msg'  => $_msg,
            'data' => $_data,
        ];
        if ($this->apiDebug) {
            $data['debug'] = use_time_memory();
        }
        return $this->outputResult($data);
    }

    /**
     * 返回错误信息
     * @access public
     * @param  string  $_msg         错误信息
     * @param  integer $_code        错误代码
     * @return json
     */
    public function outputError($_msg, $_code = 'ERROR')
    {
        $data = [
            'code' => $_code,
            'msg'  => $_msg,
        ];
        if ($this->apiDebug) {
            $data['debug'] = [
                'data' => $this->params,
                'run'  => use_time_memory(),
            ];
        }

        return $this->outputResult($data);
    }

    /**
     * 自定义输出格式
     * @access protected
     * @param  array
     * @return mixed
     */
    protected function outputResult($_params)
    {
        switch ($this->format) {
            case 'xml':
                return xml($_params);
                break;

            case 'jsonp':
                return jsonp($_params);
                break;

            default:
                return json($_params);
                break;
        }
    }
}
