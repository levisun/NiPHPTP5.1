<?php
/**
 *
 * 控制层
 * 调度控制器
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

use think\Response;
use think\facade\Env;
use app\library\Siteinfo;

class Abort
{

    public function error(int $code = 404)
    {
        $tpl = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
               'theme' . DIRECTORY_SEPARATOR . 'abort' . DIRECTORY_SEPARATOR .
               Siteinfo::theme() . DIRECTORY_SEPARATOR .
               $code . '.html';

        if (!is_file($tpl)) {
            $tpl = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                   'theme' . DIRECTORY_SEPARATOR . 'abort' . DIRECTORY_SEPARATOR .
                   $code . '.html';
        }

        $response = Response::create(file_get_contents($tpl), '', $code);
        return $response->send();
        // throw new HttpException($code);
    }
}
