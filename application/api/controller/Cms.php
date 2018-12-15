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

        if (in_array($this->action, $this->handleMethod)) {
            $this->error('[METHOD] method error');
        } elseif (in_array($this->action, $this->uploadMethod)) {
            $this->error('[METHOD] method error');
        }

        $result = $this->token()->run()->sign()->send();
        if (!is_null($result)) {
            $this->success('QUERY SUCCESS', $result);
        } else {
            $this->error('404', 'ABORT:404', '404');
        }
    }

    /**
     * 验证TOKEN
     * @access protected
     * @param
     * @return mixed
     */
    protected function token()
    {
        $http_referer = sha1(
            // $this->request->server('HTTP_REFERER') .
            $this->request->server('HTTP_USER_AGENT') .
            $this->request->ip() .
            env('root_path') .
            date('Ymd')
        );

        if (!cookie('?_ASYNCTOKEN') or !hash_equals($http_referer, cookie('_ASYNCTOKEN'))) {
            $this->debugMsg[] = cookie('_ASYNCTOKEN') . '=' . $http_referer;
            $this->error('REQUEST TOKEN ERROR');
        }

        return $this;
    }
}
