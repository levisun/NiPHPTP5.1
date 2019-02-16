<?php
/**
 *
 * 异步请求实现 - 服务层
 * Async
 * @package   NiPHP
 * @category  app\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\server;

use think\Response;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;

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
    private $authentication;

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

    private $appid;
    private $appsecret;
    private $timestamp;
    private $method;

    private   $debugLog = [];
    protected $apiCache = false;

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

    public function run()
    {
        $this->analysisHeader();
        $this->checkSign();
        $this->checkAuth();

        // 校验请求时间
        $this->timestamp = Request::param('timestamp/f', null);
        if (!$this->timestamp || $this->timestamp <= time() - 10) {
            // $this->error('request error');
        }

        // 校验API方法
        $this->method = Request::param('method');
        if ($this->method && preg_match('/^[a-z.]+$/u', $this->method)) {
            $method = 'app\logic\\' .
                      'version' . $this->version['major'] .
                      $this->module . '\\' .
                      'major' . $this->version['major'] . '\\' .
                      'minor' . $this->version['minor'] . '\\' .
                      str_replace('.', '\\', $this->method);
            // list($logic, $controller, $action) = explode('.', $this->method, 3);
            halt($method);
            # code...
        } else {
            $this->error('params-method error');
        }


        $this->initialize();

        return $this;
    }

    /**
     * 初始化
     * @access protected
     * @param
     * @return
     */
    protected function initialize(): void
    {}

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

        if ($this->sid) {
            $this->error('auth sid error');
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
                $c_f = ['appid', 'appsecret', 'sign_type', 'timestamp', 'method'];
                foreach ($params as $key => $value) {
                    if (is_string($value) && !is_null($value) && in_array($key, $c_f)) {
                        $str .= $key . '=' . $value . '&';
                    }
                }
                $str = trim($str, '&');

                if (!hash_equals(call_user_func($this->signType, $str), $this->sign)) {
                    $this->debugLog['sign_str'] = $str;
                    $this->debugLog['sign'] = $this->sign;
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
        $this->authentication = Request::header('authentication');
        if ($this->authentication && preg_match('/^[A-Za-z0-9.]+$/u', $this->authentication)) {
            // 单token值
            if (false === strpos($this->authentication, '.')) {
                $this->token = $this->authentication;
            }

            // token和session_id
            else {
                list($this->token, $this->sid) = explode('.', $this->authentication);

                // 开启session
                Config::set('session.auto_start', true);
                Config::set('session.id', $this->sid);
                Session::init(Config::get('session.'));
            }

            // 校验token合法性
            $referer = Request::header('USER-AGENT') . Request::ip() .
                       Env::get('root_path') . strtotime(date('Ymd'));
            if (!hash_equals(sha1($referer), $this->token)) {
                $this->debugLog['referer'] = $referer;
                $this->debugLog['referer::sha1'] = sha1($referer);
                $this->debugLog['this::token'] = $this->token;
                $this->error('header-authentication token error');
            }
        } else {
            $this->debugLog['authentication'] = $this->authentication;
            $this->error('header-authentication error');
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
            if (!in_array($this->format, ['json', 'pjson', 'xml'])) {
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
    protected function success(string $_msg, array $_data = [], string $_code = 'SUCCESS'): void
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
    protected function result(string $_msg, array $_data = [], string $_code = 'success'): void
    {
        $result = [
            'code'    => $_code,
            'message' => $_msg,
            'data'    => $_data,
            'time'    => date('Y-m-d H:i:s', Request::server('REQUEST_TIME'))
        ];

        $result['debug'] = $this->debugLog;

        $response = Response::create($result, $this->format, 200)->allowCache($this->apiCache);

        throw new HttpResponseException($response);
    }
}
