<?php
/**
 *
 * 控制层
 * 会员
 *
 * @package   NICMS
 * @category  app\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\controller;

use think\Response;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Env;
use think\facade\Request;
use app\library\Siteinfo;
use app\library\Template;

class My
{

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {
        // 开启session
        $session = Config::get('session');
        $session['auto_start'] = true;
        Config::set($session, 'session');

        if (!in_array(Request::action(false), ['login', 'register', 'forget', 'logout'])) {
            if (!session('?member_auth_key')) {
                $url = Request::scheme() . '://my.' . Request::rootDomain() . Request::root();
                $response = Response::create($url, 'redirect', 302);
                throw new HttpResponseException($response);
            }
        }
    }

    public function login()
    {
        # code...
    }

    public function index()
    {
        # code...
    }
}
