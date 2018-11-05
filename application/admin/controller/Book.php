<?php
/**
 *
 * 书库 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/11
 */
namespace app\admin\controller;

class Book extends Base
{

    /**
     * 管理书库
     * @access public
     * @param  string $operate
     * @return mixed
     */
    public function book($operate = '')
    {
        $tpl = $operate ? 'book_' . $operate : '';
        return $this->fetch($tpl);
    }
}
