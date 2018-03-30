<?php
/**
 *
 * 网站 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\cms\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\cms\controller;

class Index
{
    public function index()
    {
        $res = new \RandBonus;
        $r = $res->getBonus(2000000, 100);
        halt($r);
        foreach ($r as $key => $value) {
            if ($value < 0) {
                echo $value;
            }
        }
        halt(array_sum($r));

        // 15307330
        return json(array(123, 333));
    }
}
