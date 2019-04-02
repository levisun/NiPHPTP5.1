<?php
/**
 *
 * 控制层
 * admin
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

use think\facade\Config;
use think\facade\Response;
use app\library\Rbac;
use app\library\Template;

class admin extends Template
{

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->theme = 'admin' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
        $tpl_path = Config::get('app.cdn_host') . '/template/' . 'admin' . '/' . 'default' . '/';

        $this->templateReplace = [
            '{:__CSS__}'         => $tpl_path . 'css/',
            '{:__IMG__}'         => $tpl_path . 'img/',
            '{:__JS__}'          => $tpl_path . 'js/',
            '{:__STATIC__}'      => Config::get('app.cdn_host') . '/static/',
            '{:__TITLE__}'       => 'NICMS',
            '{:__KEYWORDS__}'    => 'NICMS',
            '{:__DESCRIPTION__}' => 'NICMS',
            '{:__BOTTOM_MSG__}'  => 'NICMS',
            '{:__COPYRIGHT__}'   => 'NICMS',
            '{:__SCRIPT__}'      => '',
        ];

        // 开启session
        $session = Config::get('session');
        $session['auto_start'] = true;
        Config::set($session, 'session');
    }

    public function index(string $logic = 'account', string $controller = 'login', string $action = '')
    {
        $this->__authenticate($logic, $controller, $action);

        $tpl = $logic . DIRECTORY_SEPARATOR . $controller;
        $tpl .= $action ? DIRECTORY_SEPARATOR . $action : '';

        $this->fetch($tpl);
    }

    /**
     * 验证权限
     */
    private function __authenticate(string $_logic, string $_controller, string $_action): void
    {
        if (!in_array($_logic, ['account'])) {
            // 用户权限校验
            if (session('?admin_auth_key')) {
                $result =
                (new Rbac)->authenticate(
                    session('admin_auth_key'),
                    'admin',
                    $_logic,
                    $_controller,
                    $_action
                );
            } else {
                $result = false;
            }
        } else {
            $result = true;
        }

        if (!$result) {
            $url = url('account/login');
            $response = Response::create($url, 'redirect', 302);
            throw new HttpResponseException($response);
        }
    }
}
