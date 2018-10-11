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

use think\Response;
use think\exception\HttpResponseException;

class Async
{
    protected $moduleName;              // 模块名

    protected $sign;                    // 签名
    protected $timestamp;               // 请求时间
    protected $format      = 'json';    // 返回数据类型[json|pjson|xml]

    protected $methodName;
    protected $layer       = 'logic';
    protected $class       = 'index';
    protected $action      = 'index';

    private   $logicObject = null;      // 业务类对象

    protected $apiDebug    = false;     // 调试模式
    protected $debugMsg    = [];        // 错误信息

    public function __construct()
    {
        # code...
    }

    /**
     * 执行
     * 此执行操作为业务层方法
     * @access protected
     * @param
     * @return mixed
     */
    protected function exec()
    {
        // 验证请求合法性
        $this->checkAsyncToken();

        $this->moduleName = strtolower(request()->module());
        $this->sign       = input('param.sign');
        $this->timestamp  = input('param.timestamp', 0);
        $this->format     = strtolower(input('param.format', 'json'));
        $this->methodName = strtolower(input('param.method'));
        $this->apiDebug   = APP_DEBUG;      // 显示调试信息

        // 解析method参数
        $this->analysisMethod();

        $this->auth();

        $this->checkSign();

        return call_user_func_array([$this->logicObject, $this->action], []);
    }

    /**
     * 验证权限
     * @access protected
     * @param
     * @return mixed
     */
    protected function auth()
    {
        abort(404);
    }

    /**
     * 验证异步加密签名
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkSign()
    {
        if (!$this->sign) {
            $this->error('sign error');
        }

        $params = input('param.', 'trim');
        ksort($params);

        $str = '';
        foreach ($params as $key => $value) {
            if (is_string($value) && $key !== 'sign') {
                $str .= $key . '=' . $value . '&';
            }
        }
        $str = md5(trim($str, '&'));

        if (!hash_equals($str, $this->sign)) {
            $this->debugMsg['sign'] = $str;

            $this->error('sign error');
        }
    }

    /**
     * 解析method参数
     * 参数[业务分层名.类名.方法名]
     * 参数[logic.类名.方法名]
     * 参数[logic.类名.index]
     * @access private
     * @param
     * @return mixed
     */
    private function analysisMethod()
    {
        if (!$this->methodName) {
            $this->error('[METHOD] parameter error');
        }

        $this->layer  = 'logic';
        $this->class  = 'Index';
        $this->action = 'index';

        // 参数[业务分层名.类名.方法名]
        // 参数[logic.类名.方法名] 业务分层名默认logic
        // 参数[logic.类名.index] 业务分层名默认logic 方法名默认index
        $count = count(explode('.', $this->methodName));
        if ($count == 3) {
            list($this->layer, $this->class, $this->action) = explode('.', $this->methodName, 3);
        } elseif ($count == 2) {
            list($this->class, $this->action) = explode('.', $this->methodName, 2);
        } elseif ($count == 1) {
            list($this->class) = explode('.', $this->methodName, 1);
        }

        // 检查业务分层文件是否存在
        $file_path  = env('app_path') . $this->moduleName . DIRECTORY_SEPARATOR . 'logic' . DIRECTORY_SEPARATOR;

        if ($this->layer !== 'logic') {
            $file_path .= $this->layer . DIRECTORY_SEPARATOR;
        }

        $file_path .= ucfirst($this->class) . '.php';

        if (!is_file($file_path)) {
            $this->debugMsg[] = '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';

            $this->error('[METHOD] parameter error');
        }

        // 检查方法是否存在
        $this->logicObject = logic($this->moduleName . '/' . $this->layer . '/' . $this->class);
        if (!method_exists($this->logicObject, $this->action)) {
            $this->debugMsg[] = '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';

            $this->error('[METHOD] parameter error');
        }
    }

    /**
     * 操作成功返回的数据
     * @param string $msg   提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为SUCCESS
     * @param string $type  输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function success($_msg, $_data = null, $_code = 'SUCCESS')
    {
        $this->result($_msg, $_data, $_code);
    }
    /**
     * 操作失败返回的数据
     * @param string $msg   提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为ERROR
     * @param string $type  输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function error($_msg, $_data = null, $_code = 'ERROR')
    {
        $this->result($_msg, $_data, $_code);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param  mixed  $msg    提示信息
     * @param  mixed  $data   要返回的数据
     * @param  int    $code   错误码，默认为SUCCESS
     * @return void
     * @throws HttpResponseException
     */
    protected function result($_msg, $_data = null, $_code = 'SUCCESS')
    {
        $header = [];

        $result = [
            'code' => $_code,
            'msg'  => $_msg,
            'time' => request()->server('REQUEST_TIME'),
            'data' => $_data,
        ];

        if ($this->apiDebug) {
            $result['debug'] = [
                'async'          => $this->debugMsg,
                'request_params' => input('param.', [], 'trim'),
                'method'         => $this->methodName,
                'headers'        => [
                    'cookie'          => $_COOKIE,
                    'http_referer'    => request()->server('HTTP_REFERER'),
                    'http_user_agent' => request()->server('HTTP_USER_AGENT'),
                    'ip_info'         => logic('common/IpInfo')->getInfo(),
                    'request_method'  => request()->server('REQUEST_METHOD'),
                ]
            ];
        } else {
            $header = [
                'pragma'        => 'cache',
                'cache-control' => 'max-age=28800,must-revalidate',
                'expires'       => gmdate('D, d M Y H:i:s', request()->server('REQUEST_TIME') + 28800) . ' GMT',
                'last-modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            ];
        }

        $response = Response::create($result, $this->format, 200, $header);
        throw new HttpResponseException($response);
    }

    /**
     * 生成请求令牌
     * @access public
     * @param
     * @return void
     */
    public function createAsyncToken()
    {
        $http_referer = crypt(
            request()->server('HTTP_USER_AGENT') .
            request()->url(true) .
            request()->ip(),
            '$5$rounds=5000$' . sha1(env('app_path') . app()->version() . date('Ymd')) . '$'
        );

        cookie('_ASYNCTOKEN', $http_referer);
    }

    /**
     * 验证请求令牌是否合法
     * @access private
     * @param
     * @return mixed
     */
    private function checkAsyncToken()
    {
        // 验证请求方式
        // 异步只允许 Ajax Pjax Post 请求类型
        if (!request()->isAjax() && !request()->isPjax() && !request()->isPost()) {
            abort(404);
        }

        $http_referer = crypt(
            request()->server('HTTP_USER_AGENT') .
            request()->server('HTTP_REFERER') .
            request()->ip(),
            '$5$rounds=5000$' . sha1(env('app_path') . app()->version() . date('Ymd')) . '$'
        );

        if (!cookie('?_ASYNCTOKEN') or !hash_equals($http_referer, cookie('_ASYNCTOKEN'))) {
            abort(404);
        }
    }
}
