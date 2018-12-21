<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHP
 * @category  application\api\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\api\controller;

use app\api\controller\Api;

class Cms extends Api
{

    protected function initialize()
    {
        $this->module = 'cms';
    }

    public function query()
    {
        $this->apiCache = APP_DEBUG ? false : true;

        $result = $this->token()->run()->methodAuth('query')->sign()->send();
        if (!is_null($result)) {
            $this->success('QUERY SUCCESS', $result);
        } else {
            $this->error('404', 'ABORT:404', '404');
        }
    }
}
