<?php
/**
 *
 * 异步请求实现 - 服务层
 *
 * @package   NiPHP
 * @category  application\common\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/7
 */
namespace app\common\server;

use think\facade\Request;

class Async
{
    private $request;
    private $option = [];

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

        $this->option = [
            'id' => $this->request->header('x-request-id', 'xrequestid'),
            'token' => $this->request->header('x-request-token', 'xrequesttoken'),

            'app' => $this->request->app(),

            'appid' => $this->request->param('appid/f', 1000001),
            'appsecret' => $this->request->param('appsecret', 'appsecret'),

            'sign_type' => $this->request->param('sign_type', 'md5'),
            'format' => $this->request->param('format', 'json'),
            'version' => $this->request->param('version', '1.0.1'),

            'sign' => $this->request->param('sign'),
            'method' => $this->request->param('method'),
        ];
    }

    public function run()
    {
        $this->analysis();

        print_r($this->option);

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
        if ($this->option['sign'] && preg_match('/^[A-Za-z0-9]+$/u', $this->option['sign'])) {

        }

        // 解析校验版本号
        if (preg_match('/^[0-9.]+$/u', $this->option['version'])) {
            list($major, $minor) = explode('.', $this->option['version'], 3);
            $this->option['version'] = 'v' . $major . '.' . $minor;
            unset($major, $minor);
        } else {
            $this->error('[version]参数错误');
        }
    }
}
