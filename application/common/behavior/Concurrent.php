<?php
/**
 *
 * 并发 - 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\behavior;

class Concurrent
{
    /**
     * 并发压力
     * @access public
     * @param
     * @return void
     */
    public function run()
    {
        if (APP_DEBUG) {
            return false;
        }

        if (request_block(['admin', 'wechat'])) {
            return false;
        }

        if (rand(1, 10000) === 1) {
            abort(500);
        }
    }
}
