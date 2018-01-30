<?php
/**
 *
 * 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

class Category extends Base
{

    /**
     * 管理栏目
     * @access public
     * @param
     * @return mixed
     */
    public function category()
    {
        return $this->fetch();
    }
}
