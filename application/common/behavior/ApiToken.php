<?php
/**
 *
 * API请求令牌 - 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class ApiToken
{

    /**
     * API请求令牌
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        if (request()->isAjax() || request()->isPjax() || request()->isPost()) {
            return false;
        }

        // 异步请求
        logic('common/async')->createRequireToken();
    }
}
