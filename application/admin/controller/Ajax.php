<?php
/**
 *
 * AJAX - 控制器
 *
 * @package   NiPHP
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\admin\controller;

use app\common\logic\Async;

class Ajax extends Async
{
    private $handleMethod = [
        'login',
        'logout',
        'added',
        'editor',
        'remove',
        'sort',
    ];

    private $uploadMethod = [
        'upload',
    ];

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        $this->apiCache = APP_DEBUG ? false : true;

        if (in_array($this->action, $this->handleMethod)) {
            $this->error('[METHOD] method error');
        } elseif (in_array($this->action, $this->uploadMethod)) {
            $this->error('[METHOD] method error');
        }

        $result = $this->run()->token()->auth()->send();
        if (!is_null($result)) {
            $this->success('QUERY SUCCESS', $result);
        } else {
            $this->error('404', 'ABORT:404', '404');
        }
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function handle()
    {
        if (!in_array($this->action, $this->handleMethod)) {
            $this->error('[METHOD] method error');
        }

        $result = $this->run()->token()->auth()->send();
        if ($result === true) {
            $this->success(lang('exec success'), $result);
        } else {
            $this->error($result);
        }
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        if (!in_array($this->action, $this->uploadMethod)) {
            $this->error('[METHOD] method error');
        }

        $result = $this->run()->token()->auth()->send();
        if ($result === false) {
            return $this->error($this->errorMsg);
        } elseif (is_string($result)) {
            return $this->error($result);
        } else {
            if (input('param.type') === 'ckeditor') {
                return json([
                    'uploaded' => true,
                    'url' => $result['domain'] . $result['save_dir'] . $result['file_name'],
                ]);
            } else {
                $this->success(lang('upload success'), $result);
            }

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
            $this->error('REQUEST TOKEN ERROR');
        }

        return $this;
    }

    /**
     * 验证Auth
     * @access protected
     * @param
     * @return mixed
     */
    protected function auth()
    {
        // 权限验证
        if ($this->action != 'login') {
            // 是否登录
            if (!session('?' . config('user_auth_key'))) {
                abort(404);
            }

            // 登录权限信息
            if (logic('common/Rbac')->checkAuth(
                session(config('user_auth_key')),
                'admin',
                $this->layer,
                $this->class,
                $this->action
            )) {
                trace('[NO AUTHORITY] ' . $this->layer . $this->class . $this->action, 'warning');
                abort(404);
            }
        }

        return $this;
    }
}
