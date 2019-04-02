<?php
/**
 *
 * 服务层
 * 异步请求实现
 * Async
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\Container;
use think\Response;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Log;
use think\facade\Request;
use think\facade\Session;
use app\library\Base64;
use app\model\Session as SessionModel;

class Api
{

    /**
     * HEADER 指定接收类型
     * 包含[域名 版本 返回类型]
     * application/vnd.tp5.v1.0.1+json
     * @var string
     */
    private $accept;

    /**
     * HEADER 授权信息
     * 包含[token sessid]
     * f0c4b4105d740747d44ac6dcd78624f906202706.
     * @var string
     */
    private $authorization;

    /**
     * 版本号
     * 解析[accept]获得
     * @var array
     */
    private $version = [
        'major' => '1',
        'minor' => '0'
    ];

    /**
     * 开启版本控制
     * @var bool
     */
    private $openVersion = false;

    /**
     * 返回数据类型
     * 解析[accept]获得
     * @var string
     */
    private $format = 'json';

    /**
     * 请求令牌
     * 解析[authentication]获得
     * @var string
     */
    private $token;

    /**
     * session_id
     * 解析[authentication]获得
     * @var string
     */
    private $sid;

    /**
     * 签名类型
     * @var string
     */
    private $signType;

    /**
     * 签名
     * @var string
     */
    private $sign;

    /**
     * 模块名
     * @var string
     */
    private $module = 'cms';

    /**
     * 执行类与方法
     * @var array
     */
    protected $exec = [];

    /**
     * 调试信息
     * @var array
     */
    private $debugLog = [];

    /**
     * 调试开关
     * @var bool
     */
    protected $debug = true;

    /**
     * 浏览器数据缓存开关
     * @var bool
     */
    protected $cache = true;

    /**
     * 浏览器数据缓存时间
     * @var int
     */
    protected $expire = 1140;


    protected $appid;
    protected $appsecret;
    protected $timestamp;
    protected $method;


    /**
     * 构造方法
     * @access public
     * @param
     * @return void
     */
    public function __construct()
    {
    }

    public function setModule(string $_name)
    {
        $this->module = $_name;
        return $this;
    }

    /**
     * 运行
     * @return void
     */
    public function run(): void
    {
        $this->initialize();

        // 执行类方法
        $result = call_user_func_array([(new $this->exec['class']), $this->exec['action']], []);

        if (!is_array($result) && empty($result['msg'])) {
            $this->error($this->exec['class'] . '::' . $this->exec['action'] . '() 返回数据错误');
        }

        // 调试与缓存设置
        // 调试模式 返回数据没有指定默认关闭
        $this->debug  = isset($result['debug']) ? !!$result['debug'] : false;

        // 浏览器缓存 返回数据没有指定默认开启
        $this->cache  = isset($result['cache']) ? !!$result['cache'] : true;
        // 当调试模式开启时关闭缓存
        $this->cache = $this->debug ? false : $this->cache;

        // 浏览器缓存时间
        $this->expire = Config::get('cache.expire');
        $this->expire = isset($result['expire']) ? $result['expire'] : $this->expire;
        $this->expire = $this->expire <= 0 ? $this->expire : $this->expire;

        $this->success($result['msg'], isset($result['data']) ? $result['data'] : []);
    }

    /**
     * 初始化
     * @access protected
     * @param
     * @return
     */
    protected function initialize(): void
    {
        $this->analysisHeader();
        $this->checkSign();
        $this->checkAuth();

        // 校验请求时间
        $this->timestamp = Request::param('timestamp/f', time());
        if (!$this->timestamp || $this->timestamp <= time() - 10) {
            $this->error('request timeout');
        }

        // 校验API方法
        $this->method = Request::param('method');
        if ($this->method && preg_match('/^[a-z.]+$/u', $this->method)) {
            list($logic, $class, $action) = explode('.', $this->method, 3);

            if ($this->openVersion) {
                $method = 'app\logic\\' . $this->module . '\\' .
                          'v' . $this->version['major'] . 'm' . $this->version['minor'] . '\\' .
                          $logic . '\\' . ucfirst($class);
            } else {
                $method = 'app\logic\\' . $this->module . '\\' .
                          $logic . '\\' . ucfirst($class);
            }


            // 校验类是否存在
            if (!class_exists($method)) {
                $this->debugLog['method not found'] = $method;
                $this->error('method not found');
            }

            // 校验类方法是否存在
            if (!method_exists($method, $action)) {
                $this->error('class ' . $method . ' does not have a method ' . $action);
            }

            // 加载语言包
            if ($this->openVersion) {
                $lang = app()->getAppPath(). 'server' . DIRECTORY_SEPARATOR .
                    $this->module . DIRECTORY_SEPARATOR .
                    'v' . $this->version['major'] . 'm' . $this->version['minor'] . DIRECTORY_SEPARATOR .
                    'lang' . DIRECTORY_SEPARATOR . Lang::detect() . '.php';
            } else {
                $lang = app()->getAppPath(). 'server' . DIRECTORY_SEPARATOR .
                    $this->module . DIRECTORY_SEPARATOR .
                    'lang' . DIRECTORY_SEPARATOR . Lang::detect() . '.php';
            }

            Lang::load($lang);

            $this->exec = [
                'class'  => $method,
                'action' => $action
            ];
        } else {
            $this->error('params-method error');
        }
    }

    /**
     * 校验权限
     * 继承类
     * @access protected
     * @param
     * @return $this
     */
    protected function checkAuth()
    {
        $this->appid     = Request::param('appid/f', 1000001);
        if ($this->appid && is_numeric($this->appid)) {
            $this->appsecret = Request::param('appsecret', 'appsecret');
            if ($this->appsecret && preg_match('/^[A-Za-z0-9]+$/u', $this->appsecret)) {
                # code...
            } else {
                $this->error('auth-appsecret error');
            }
        } else {
            $this->error('auth-appid error');
        }

        // 开启session
        if ($this->sid) {
            // 校验session合法性
            $session_id =
            SessionModel::where([
                ['session_id', '=', $this->sid]
            ])
            ->value('session_id');
            if ($session_id) {
                // 开启session
                if (!session_id()) {
                    $session = Config::get('session');
                    $session['auto_start'] = true;
                    $session['id'] = $this->sid;
                    Config::set($session, 'session');

                    session_id($this->sid);
                    session_start();
                    session_write_close();
                }
            } else {
                $this->error('auth sid error');
            }
        }
    }

    /**
     * 校验签名类型与签名合法性
     * @access protected
     * @param
     * @return $this
     */
    protected function checkSign()
    {
        // 校验签名类型
        $this->signType = Request::param('sign_type', 'md5');
        if ($this->signType && function_exists($this->signType)) {
            // 校验签名合法性
            $this->sign = Request::param('sign');
            if ($this->sign && preg_match('/^[A-Za-z0-9]+$/u', $this->sign)) {
                $params = Request::param('', '', 'trim');
                ksort($params);

                $str = '';
                $c_f = ['appid', 'sign_type', 'timestamp', 'method'];
                foreach ($params as $key => $value) {
                    if (is_string($value) && !is_null($value) && in_array($key, $c_f)) {
                        $str .= $key . '=' . $value . '&';
                    }
                }
                $str = trim($str, '&');

                if (!hash_equals(call_user_func($this->signType, $str), $this->sign)) {
                    $this->debugLog['sign_str'] = $str;
                    $this->debugLog['sign'] = call_user_func($this->signType, $str);
                    $this->error('params-sign check error');
                }
            } else {
                $this->debugLog['sign'] = $this->sign;
                $this->error('params-sign error');
            }
        } else {
            $this->debugLog['sign_type'] = $this->signType;
            $this->error('params-sign_type error');
        }
    }

    /**
     * 解析header信息
     * @access private
     * @param
     * @return void
     */
    private function analysisHeader(): void
    {
        // 解析token令牌和session_id
        $this->authorization = Request::header('authorization');
        if ($this->authorization && preg_match('/^[A-Za-z0-9.]+$/u', $this->authorization)) {
            $this->authorization = Base64::decrypt($this->authorization, 'authorization');

            // 单token值
            if (false === strpos($this->authorization, '.')) {
                $this->token = $this->authorization;
            }

            // token和session_id
            else {
                list($this->token, $this->sid) = explode('.', $this->authorization);
            }

            // 校验token合法性
            $referer = Request::header('USER-AGENT') . Request::ip() .
                       app()->getRootPath() . strtotime(date('Ymd'));
            if (!hash_equals(sha1(Base64::encrypt($referer, 'authorization')), $this->token)) {
                $this->debugLog['referer'] = $referer;
                $this->debugLog['referer::sha1'] = sha1(Base64::encrypt($referer, 'authorization'));
                $this->debugLog['this::token'] = $this->token;
                $this->error('header-authorization token error');
            }
        } else {
            $this->debugLog['authorization'] = $this->authorization;
            $this->error('header-authorization error');
        }


        // 解析版本号与返回数据类型
        $this->accept = Request::header('accept');
        if ($this->accept && preg_match('/^[A-Za-z0-9.\/\+]+$/u', $this->accept)) {
            // 过滤多余信息
            $accept = str_replace('application/vnd.', '', $this->accept);

            // 校验域名合法性
            list($domain, $accept) = explode('.', $accept, 2);
            list($root) = explode('.', Request::rootDomain(), 2);
            if (!hash_equals($domain, $root)) {
                $this->error('header-accept domain error');
            }
            unset($doamin, $root);

            // 取得版本与数据类型
            list($version, $this->format) = explode('+', $accept, 2);
            if ($version && preg_match('/^[v0-9.]+$/u', $version)) {
                $version = substr($version, 1);
                list($major, $minor) = explode('.', $version, 3);
                $this->version = [
                    'major' => $major,
                    'minor' => $minor
                ];
                unset($version, $major, $minor);
            } else {
                $this->debugLog['version'] = $version;
                $this->error('header-accept version error');
            }
            // 校验返回数据类型
            if (!in_array($this->format, ['json', 'xml'])) {
                $this->debugLog['format'] = $this->format;
                $this->error('header-accept format error');
            }

            unset($accept);
        } else {
            $this->debugLog['accept'] = $this->accept;
            $this->error('header-accept error');
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
    protected function success(string $_msg, $_data = [], string $_code = 'SUCCESS'): void
    {
        $this->result($_msg, $_data, $_code);
    }
    /**
     * 操作失败返回的数据
     * @access protected
     * @param  string  $msg  提示信息
     * @param  integer $code 错误码，默认为ERROR
     * @return void
     * @throws HttpResponseException
     */
    protected function error(string $_msg, string $_code = 'ERROR'): void
    {
        $this->result($_msg, [], $_code);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param  mixed  $msg    提示信息
     * @param  mixed  $data   要返回的数据
     * @param  int    $code   错误码，默认为0
     * @return void
     * @throws HttpResponseException
     */
    protected function result(string $_msg, $_data = [], string $_code = 'SUCCESS'): void
    {
        $result = [
            'code'    => $_code,
            'data'    => $_data,
            'debug'   => $this->debugLog,
            'expire'  => $this->cache ? date('Y-m-d H:i:s', time() + $this->expire + 60) : '0',
            'message' => $_msg
        ];
        $result = array_filter($result);

        // 记录日志
        $this->writeLog($result);

        if ($this->debug === false) {
            unset($result['debug']);
        }

        $headers = [];
        if (APP_DEBUG === false && $this->cache === true && $this->expire && $_code == 'SUCCESS') {
            $headers = [
                'Cache-Control' => 'max-age=' . $this->expire . ',must-revalidate',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'Expires'       => gmdate('D, d M Y H:i:s', time() + $this->expire) . ' GMT'
            ];
        }

        $response = Response::create($result, $this->format)->header($headers);
        throw new HttpResponseException($response);
    }

    /**
     * 调试日志
     * @access private
     * @param
     * @return void
     */
    private function writeLog(array $result)
    {
        $log = '[API] IP:' . Request::ip() .
                ' TIME:' . number_format(microtime(true) - Container::pull('app')->getBeginTime(), 6) . 's' .
                ' MEMORY:' . number_format((memory_get_usage() - Container::pull('app')->getBeginMem()) / 1024 / 1024, 2) . 'MB' .
                ' CACHE:' . Container::pull('cache')->getReadTimes() . ' reads,' . Container::pull('cache')->getWriteTimes() . ' writes';

        if (APP_DEBUG) {
            $log .= PHP_EOL . 'PARAM:' . json_encode(Request::param('', '', 'trim'), JSON_UNESCAPED_UNICODE);
            $log .= PHP_EOL . 'DEBUG:' . json_encode($this->debugLog, JSON_UNESCAPED_UNICODE);
            $log .= PHP_EOL . 'RESULT:' . json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        Log::record($log, 'alert');
    }
}
