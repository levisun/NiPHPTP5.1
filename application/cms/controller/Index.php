<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Index.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index
{
    public function index()
    {
        // halt(1002%10);


        $t = new \PHPExcel;
        halt($t);
    }
}
