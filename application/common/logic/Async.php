<?php
/**
 *
 * 异步请求实现 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\common\logic
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
    protected $moduleName;                                                      // 模块名

    protected $appid;
    protected $appsecret;
    protected $sign;                                                            // 签名
    protected $sign_type   = 'md5';                                             // 签名类型
    protected $timestamp   = 0;                                                 // 请求时间
    protected $format      = 'json';                                            // 返回数据类型[json|pjson|xml]
    protected $version     = null;                                              // 版本号

    protected $methodName;                                                      // 执行方法名
    protected $layer       = 'logic';                                           // 业务名
    protected $class       = 'index';                                           // 类名
    protected $action      = 'index';                                           // 方法名

    private   $logicObject = null;                                              // 业务类对象

    protected $apiDebug    = false;                                             // 调试模式
    protected $debugMsg    = [];                                                // 错误信息


    public function handle()
    {
        // 验证请求合法性
        $this->checkAsyncToken();

        $this->moduleName = strtolower(request()->module());                    // 模块名称
        $this->appid      = input('param.appid');                               //
        $this->appsecret  = input('param.appsecret');                           //
        $this->sign       = input('param.sign');                                // 请求数据签名
        $this->sign_type  = input('param.sign_type', 'md5');                    // 签名类型
        $this->timestamp  = input('param.timestamp/f', time());                 // 请求时间戳
        $this->format     = strtolower(input('param.format', 'json'));          // 返回数据类型
        $this->methodName = strtolower(input('param.method'));                  // 请求API方法名
        $this->version    = input('param.version', null);                       // 版本号
        $this->apiDebug   = APP_DEBUG;                                          // 显示调试信息


        $this->analysisMethod();                                                // 解析method参数
        $this->auth();                                                          // 请求权限校验
        $this->sign();                                                          // 请求数据签名校验

        return call_user_func_array([$this->logicObject, $this->action], []);
    }

    /**
     * 生成请求令牌
     * @access public
     * @param
     * @return void
     */
    public function createAsyncToken()
    {
        if (!cookie('?_ASYNCTOKEN')) {
            $http_referer = sha1(
                // request()->url(true) .
                request()->server('HTTP_USER_AGENT') .
                request()->ip() .
                env('root_path') .
                date('Ymd')
            );

            cookie('_ASYNCTOKEN', $http_referer, strtotime(date('Y-m-d 23:59:59')) - time());
        }
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
            $this->error('REQUEST METHOD ERROR');
        }

        $http_referer = sha1(
            // request()->server('HTTP_REFERER') .
            request()->server('HTTP_USER_AGENT') .
            request()->ip() .
            env('root_path') .
            date('Ymd')
        );

        if (!cookie('?_ASYNCTOKEN') or !hash_equals($http_referer, cookie('_ASYNCTOKEN'))) {
            $this->error('REQUEST TOKEN ERROR');
        }
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
    protected function sign()
    {
        if (!$this->sign) {
            $this->error('sign error');
        }

        $params = input('param.', '', 'trim');
        ksort($params);

        $str = '';
        foreach ($params as $key => $value) {
            if (is_string($value) && !in_array($key, ['sign', 'p'])) {
                $str .= $key . '=' . $value . '&';
            }
        }
        $str = trim($str, '&');
        $str = call_user_func($this->sign_type, $str);

        if (!hash_equals($str, $this->sign)) {
            $this->debugMsg['sign'] = $str;
            trace('[SIGN ERROR] ' . $this->sign . '::' . $str, 'alert');
            $this->error('SIGN ERROR');
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
        $file_path = env('app_path') . $this->moduleName . DIRECTORY_SEPARATOR . 'logic' . DIRECTORY_SEPARATOR;
        // 分层目录
        $file_path .= $this->layer !== 'logic' ? $this->layer . DIRECTORY_SEPARATOR : '';
        // 版本目录
        $file_path .= $this->version ? $this->version . DIRECTORY_SEPARATOR : '';

        $file_path .= ucfirst($this->class) . '.php';

        if (!is_file($file_path)) {
            $this->debugMsg[] = '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';
            $this->error('[METHOD PARAMETER ERROR]');
        }

        // 检查方法是否存在
        $this->logicObject = logic($this->moduleName . '/' . $this->layer . '/' . $this->class);
        if (!method_exists($this->logicObject, $this->action)) {
            $this->debugMsg[] = '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';
            $this->error('[METHOD PARAMETER ERROR]');
        }
    }

    /**
     * 操作成功返回的数据
     * @access protected
     * @param  string  $msg  提示信息
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 错误码，默认为SUCCESS
     * @return void
     * @throws HttpResponseException
     */
    protected function success($_msg, $_data = [], $_code = 'SUCCESS')
    {
        $this->result($_msg, $_data, $_code);
    }
    /**
     * 操作失败返回的数据
     * @access protected
     * @param  string  $msg  提示信息
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 错误码，默认为ERROR
     * @return void
     * @throws HttpResponseException
     */
    protected function error($_msg, $_data = [], $_code = 'ERROR')
    {
        $this->result($_msg, $_data, $_code);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access private
     * @param  mixed   $msg  提示信息
     * @param  mixed   $data 要返回的数据
     * @param  integer $code 错误码，默认为SUCCESS
     * @return void
     * @throws HttpResponseException
     */
    private function result($_msg, $_data = [], $_code = 'SUCCESS')
    {
        $header = [];

        $result = [
            'code'    => $_code,
            'msg'     => $_msg,
            'data'    => $_data,

            'log' => [
                'request ip'   => logic('common/IpInfo')->getInfo(),
                'request time' => date('Y-m-d H:i:s', request()->server('REQUEST_TIME')),
                'runtime'      => number_format(microtime(true) - app()->getBeginTime(), 6) . '秒',
                'memory'       => number_format((memory_get_usage() - app()->getBeginMem()) / 1024 / 1024, 2) . 'MB',
            ]
        ];

        if ($this->apiDebug) {
            $result['log']['server'] = [
                'sql query' => \think\Db::$queryTimes . '条查询 ' . \think\Db::$executeTimes . '条写入',
                'db cache'  => app('cache')->getReadTimes() . '次读取 ' . app('cache')->getWriteTimes() . '次写入',
                'include'   => count(get_included_files()) . '个文件'
            ];
            $result['async']  = $this->debugMsg;
            $result['params'] = input('param.', [], 'trim');
            $result['method'] = $this->methodName;
        }

        $response = Response::create($result, $this->format, 200)->allowCache(!APP_DEBUG);

        throw new HttpResponseException($response);
    }
}
