<?php
/**
 *
 * 控制层
 * admin
 *
 * @package   NiPHP
 * @category  app\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\controller;

use app\library\Template;

class admin
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
        Config::set('session.auto_start', true);
        session(Config::get('session.'));
    }

    public function index(string $logic = 'account', string $name = 'login')
    {


        return (new Template)->fetch($logic . DIRECTORY_SEPARATOR . $name);
    }
}
