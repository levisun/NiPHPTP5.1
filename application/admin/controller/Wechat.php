<?php
/**
 *
 * 微信 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2028/03
 */
namespace app\admin\controller;

use app\admin\controller\Base;

class Wechat extends Base
{

    /**
     * 关键词回复
     * @access public
     * @param
     * @return mixed
     */
    public function keyword($operate = '')
    {
        $this->assign('button_search', 1);
        $this->assign('type', 0);

        $tpl = $operate ? 'keyword_' . $operate : '';
        return $this->fetch($tpl);
    }
}
