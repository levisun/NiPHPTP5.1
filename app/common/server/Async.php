<?php
/**
 *
 * 异步请求实现 - 服务层
 *
 * @package   NiPHP
 * @category  app\common\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/7
 */
namespace app\common\server;

use think\Response;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;

class Async
{
    private   $request;
    private   $params = [
        'app_name'  => '',
        'accept'    => '',
        'auth'      => '',
        'appid'     => '',
        'appsecret' => '',
        'sign'      => '',
        'timestamp' => '',
        'token'     => null,
        'sid'       => null,
        'format'    => 'json',
        'sign_type' => 'md5',
    ];
    private   $debugLog = [];
    protected $apiCache = false;

    /**
     * 构造方法
     * @access public
     * @param  Request $_request Request对象
     * @return void
     */
    public function __construct(Request $_request = null)
    {
        $this->request = is_null($_request) ? Request::instance() : $_request;
        $this->request->filter('strip_tags');

        $params = [
            'app_name'  => $this->request->app(),

            'accept'    => $this->request->header('accept'),
            'auth'      => $this->request->header('authentication', null),

            'appid'     => $this->request->param('appid/f', 1000001),
            'appsecret' => $this->request->param('appsecret', 'appsecret'),
            'sign_type' => $this->request->param('sign_type', 'md5'),
            'sign'      => $this->request->param('sign'),
            'timestamp' => $this->request->param('timestamp/f', time()),
            'method'    => $this->request->param('method'),

            'token'     => null,
            'sid'       => null,
            'format'    => 'json',
            'sign_type' => 'md5',
        ];
        $this->params = array_merge($this->params, $params);
    }

    public function __set($_name, $_value)
    {
        $this->params[$_name] = $_value;
    }

    public function run()
    {
        $this->analysis();

        // 校验方法是否存在
        if (is_file(Env::get('root_path') . 'app' . DIRECTORY_SEPARATOR . $this->params['method'] . '.php')) {
            $class = '\app\\' . $this->params['method'];
            if (!class_exists($class) || !method_exists($class, $this->params['action'])) {
                $this->debug('METHOD参数错误 [' . $class . '::' . $this->params['action'] . ']', __LINE__);
                $this->error('METHOD参数错误');
            }
        } else {
            $this->debug('METHOD参数错误 [app' . DIRECTORY_SEPARATOR . $this->params['method'] . '.php]', __LINE__);
            $this->error('METHOD参数错误');
        }

        // 初始化语言
        $lang_path  = Env::get('root_path') . 'app' . DIRECTORY_SEPARATOR . $this->params['app_name'];
        $lang_path .= DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
        $lang_path .= Lang::detect() . '.php';
        Lang::load($lang_path);

        // 初始化SESSION
        if ($this->params['sid']) {
            Config::set('session.auto_start', true);
            Config::set('session.id', $this->params['sid']);
            Session::init(Config::get('session.'));
        }

        print_r($lang_path);

        return $this;
    }

    /**
     * 解析校验参数合法性
     * @access private
     * @param
     * @return void
     */
    private function analysis()
    {
        // 解析TOKEN令牌和SID
        if ($this->params['auth'] && preg_match('/^[A-Za-z0-9.]+$/u', $this->params['auth'])) {
            if (false === strpos($this->params['auth'], '.')) {
                $this->params['token'] = $this->params['auth'];
            } else {
                list($this->params['token'], $this->params['sid']) = explode('.', $this->params['auth']);
            }

            $referer = $this->request->header('USER-AGENT') . $this->request->ip() .
                       Env::get('root_path') . strtotime(date('Ymd'));

            if (!hash_equals(sha1($referer), $this->params['token'])) {
                $this->error('TOKEN参数错误');
            }
        } else {
            $this->error('TOKEN参数错误');
        }

        // 解析版本号与数据类型
        $accept = str_replace('application/vnd.', '', $this->params['accept']);
        list($domain, $accept) = explode('.', $accept, 2);
        list($root) = explode('.', $this->request->rootDomain(), 2);
        if (!hash_equals($domain, $root)) {
            $this->error('VERSION参数错误');
        }
        unset($doamin, $root);

        list($this->params['version'], $this->params['format']) = explode('+', $accept, 2);
        unset($accept);



        // 解析校验版本号
        if ($this->params['version'] && preg_match('/^[v0-9.]+$/u', $this->params['version'])) {
            list($major, $minor) = explode('.', $this->params['version'], 3);
            $this->params['version'] = $major;
            unset($major, $minor);
        } else {
            $this->error('VERSION参数错误');
        }

        // 解析方法
        if ($this->params['method'] && preg_match('/^[a-z.]+$/u', $this->params['method'])) {
            list($logic, $class, $action) = explode('.', $this->params['method'], 3);
            $this->params['method'] = $this->params['app_name'] . '\\logic\\' .
                                      $this->params['version'] . '\\' .
                                      $logic . '\\' . ucfirst($class);

            $this->params['action'] = $action;
            unset($logic, $class, $action);
        } else {
            $this->error('METHOD参数错误');
        }
    }

    /**
     * 错误日志
     * @access protected
     * @param  string $_msg
     * @param  string $_link
     * @return void
     */
    protected function debug($_msg, $_link)
    {
        $this->debugLog[] = [
            '{LINK: ' . $_link . '}   LOG: ' . $_msg
        ];
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
     * @access protected
     * @param  mixed  $msg    提示信息
     * @param  mixed  $data   要返回的数据
     * @param  int    $code   错误码，默认为0
     * @return void
     * @throws HttpResponseException
     */
    protected function result($_msg, $_data = [], $_code = 'SUCCESS')
    {
        $result = [
            'code'    => $_code,
            'message' => $_msg,
            'data'    => $_data,
            'time'    => date('Y-m-d H:i:s', $this->request->server('REQUEST_TIME'))
        ];

        $result['debug'] = $this->debugLog;

        $response = Response::create($result, $this->params['format'], 200)->allowCache($this->apiCache);

        throw new HttpResponseException($response);
    }
}
