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

use app\common\logic\Async;

class Book extends Async
{

    protected function initialize()
    {
        $this->module = 'book';
    }

    public function query()
    {
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
        // 验证请求方式
        // 异步只允许 Ajax Pjax Post 请求类型
        if (!$this->request->isAjax() && !$this->request->isPjax() && !$this->request->isPost()) {
            $this->error('REQUEST METHOD ERROR');
        }

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
