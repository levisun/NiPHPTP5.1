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
    protected $appid;               // ID[数据库中获取]
    protected $appsecret;           // 密钥[数据库中获取]
    protected $token;               // 令牌[数据库中获取]

    protected $sign;                // 签名
    protected $timestamp;           // 请求时间
    protected $format = 'json';     // 返回数据类型[json|pjson|xml]

    protected $method;              // 方法

    protected $encodingaeskey;      // 加解密密钥[数据库中获取]

    protected $module;              // 模块[自动获取]

    protected $object;              // 实例化的方法


    protected $layer  = 'logic';    // 业务类所在层
    protected $class  = 'index';    // 业务逻辑类名
    protected $action = 'index';    // 业务类方法

    protected $apiDebug = true;     // 调试信息

    protected $errorMsg;            // 错误信息

    public function __construct()
    {
        // 公共参数赋值
        $this->token     = input('param.token');

        $this->sign      = input('param.sign');
        $this->timestamp = input('param.timestamp', 0);
        $this->method    = input('param.method');
        $this->format    = input('param.format');

        $this->module    = strtolower(request()->module());

        // 调试
        $this->apiDebug  = APP_DEBUG;

        // IP地区信息[记录自己的IP地址库]
        logic('common/IpInfo')->getInfo();
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
        if ($this->validate()) {
            return call_user_func_array([$this->object, $this->action], []);
        } else {
            return false;
        }
    }

    /**
     * 验证数据合法性
     * @access protected
     * @param
     * @return mixed
     */
    protected function validate()
    {
        // 验证请求方式
        // 异步只允许 Ajax Pjax Post 请求类型
        if (!request()->isAjax() && !request()->isPjax() && !request()->isPost()) {
            $this->errorMsg = 'request mode error';
            return false;
        }

        // 验证需求令牌 此令牌程序生成[createRequireToken()]
        $result = $this->checkRequireToken();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 验证Token
        $result = $this->checkToken();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 请求时间
        $result = $this->checkTimestamp();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 验证Sign
        $result = $this->checkSign();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 验证参数 缺少请求执行方法参数
        // 自动查找业务层方法
        $result = $this->autoFindMethod();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 验证Auth权限
        $result = $this->checkAuth();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
        }

        // 验证Logic文件是否存在
        $result = $this->checkLogicFile();
        if ($result !== true) {
            $this->errorMsg = $result;
            return false;
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
        return true;
    }


    /**
     * 验证请求时间戳
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkTimestamp()
    {
        if ($this->timestamp <= strtotime('-1 days')) {
            return 'request timeout';
        }

        return true;
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
            // return 'sign error';
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

        if (hash_equals($str, $this->sign)) {
            return true;
        } else {
            return 'sign error';
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
            $token =
            model('common/config')
            ->where([
                ['name', '=', 'ajax_token']
            ])
            ->cache('_COMMON_LOGIC_ASYNC_CHECKTOKEN')
            ->value('value');

            if ($token !== $this->token) {
                return 'token error';
            }
        } else {
            return 'token error';
        }

        return true;
    }

    /**
     * 生成请求令牌
     * @access public
     * @param
     * @return void
     */
    public function createRequireToken()
    {
        $http_referer = md5(
            app()->version() .
            request()->header('user_agent') .
            env('app_path') .
            date('Ymd') .
            json_encode(logic('common/IpInfo')->getInfo())
        );

        cookie('_ASYNCTOKEN', $http_referer);
    }

    /**
     * 验证请求令牌是否合法
     * @access private
     * @param
     * @return mixed
     */
    private function checkRequireToken()
    {
        if (!cookie('?_ASYNCTOKEN')) {
            return 'request token error1';
        }

        $http_referer = md5(
            app()->version() .
            request()->header('user_agent') .
            env('app_path') .
            date('Ymd') .
            json_encode(logic('common/IpInfo')->getInfo())
        );

        if (hash_equals($http_referer, cookie('_ASYNCTOKEN'))) {
            return true;
        }

        return 'request token error';
    }

    /**
     * 验证Logic文件是否存在
     * @access private
     * @param
     * @return mixed
     */
    private function checkLogicFile()
    {
        // 检查业务分层文件是否存在
        $file_path  = env('app_path') . $this->module . DIRECTORY_SEPARATOR;
        $file_path .= 'logic' . DIRECTORY_SEPARATOR;
        if ($this->layer !== 'logic') {
            $file_path .= $this->layer . DIRECTORY_SEPARATOR;
        }
        $file_path .= $this->class . '.php';
        if (!is_file($file_path)) {
            return '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';
        }

        // 检查方法是否存在
        $this->object = logic($this->module . '/' . $this->layer . '/' . $this->class);
        if (is_object($this->object) && method_exists($this->object, $this->action)) {
            return true;
        } else {
            return '$' . $this->class . '->' . $this->action . '() logic doesn\'t exist';
        }
    }

    /**
     * 根据METHOD命名规范查找业务层方法
     * @access private
     * @param
     * @return mixed
     */
    private function autoFindMethod()
    {
        if (!$this->method) {
            return '[METHOD] parameter error';
        }

        // 参数[业务分层名.类名.方法名]
        // 参数[logic.类名.方法名] 业务分层名默认logic
        // 参数[logic.类名.index] 业务分层名默认logic 方法名默认index
        $count = count(explode('.', $this->method));
        if ($count == 3) {
            list($this->layer, $this->class, $this->action) = explode('.', $this->method, 3);
        } elseif ($count == 2) {
            list($this->class, $this->action) = explode('.', $this->method, 2);
        } elseif ($count == 1) {
            list($this->class) = explode('.', $this->method, 1);
        }

        return true;
    }

    /**
     * 返回信息
     * @access protected
     * @param  string  $_msg         提示信息
     * @param  integer $_code        代码
     * @param  array   $_data        返回数据
     * @return json
     */
    protected function outputData($_msg, $_data = [], $_code = 'SUCCESS')
    {
        return $this->outputResult([
            'code' => $_code,
            'msg'  => $_msg,
            'data' => $_data,
        ]);
    }

    /**
     * 返回错误信息
     * @access protected
     * @param  string  $_msg         错误信息
     * @param  integer $_code        错误代码
     * @return json
     */
    protected function outputError($_msg, $_code = 'ERROR')
    {
        return $this->outputResult([
            'code' => $_code,
            'msg'  => $_msg,
        ]);
    }

    /**
     * 自定义输出格式
     * @access protected
     * @param  array
     * @return mixed
     */
    protected function outputResult($_params)
    {
        if ($this->apiDebug) {
            $_params['METHOD']         = $this->method;
            $_params['REFERER']        = request()->header('referer');
            $_params['IP_INFO']        = logic('common/IpInfo')->getInfo();
            $_params['COOKIE']         = $_COOKIE;
            $_params['USER_AGENT']     = request()->header('user_agent');
            $_params['TIME_MEMORY']    = use_time_memory();
            $_params['REQUEST_PARAMS'] = input('param.', 'trim');
            $_params['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        }

        $header = [
            'pragma'        => 'cache',
            'cache-control' => 'max-age=3600,must-revalidate',
            'expires'       => gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + 3600) . ' GMT',
            'last-modified' => gmdate('D, d M Y H:i:s') . ' GMT',
        ];

        switch ($this->format) {
            case 'xml':
                return xml($_params)
                ->code(201)
                ->allowCache(false)
                ->header($header);
                break;

            case 'jsonp':
                return jsonp($_params)
                ->code(201)
                ->allowCache(false)
                ->header($header);
                break;

            default:
                return
                json($_params)
                ->code(201)
                ->allowCache(false)
                ->header($header);
                break;
        }
    }
}
