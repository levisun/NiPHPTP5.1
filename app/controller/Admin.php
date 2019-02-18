<?php
/**
 *
 * admin - 控制层层
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

use app\server\Tpl;

class admin
{

    public function index(string $logic = 'account', string $name = 'login')
    {
        return (new Tpl)->fetch($logic . DIRECTORY_SEPARATOR . $name);
    }
}
