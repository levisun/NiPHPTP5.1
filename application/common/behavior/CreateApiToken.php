<?php
/**
 *
 * 生成API请求令牌 - 行为
 *
 * @package   NiPHPCMS
 * @category  application\common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class CreateApiToken
{

    /**
     * API请求令牌
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        // 阻挡Ajax Pjax Post类型请求
        // 阻挡common模块请求
        if (request_block()) {
            return true;
        }

        // 异步请求
        logic('common/async')->createAsyncToken();
    }
}
