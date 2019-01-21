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
namespace app\api\logic;

use app\common\logic\Async;

class Admin extends Async
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

    protected function initialize()
    {
        $this->module = 'admin';

        // 加载项目函数库
        include_once env('app_path') . 'admin' . DIRECTORY_SEPARATOR . 'common.php';

        // 加载项目配置
        $config = include env('app_path') . 'admin' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        foreach ($config as $name => $value) {
            config($name, $value);
        }

        config('session.auto_start', true);
        config('session.id', $this->sid);
        session(config('session.'));
    }

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        $this->apiCache = APP_DEBUG ? false : true;

        $result = $this->run()->methodAuth('query')->auth()->send();
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
        $result = $this->run()->methodAuth('handle')->auth()->send();
        if ($result === true) {
            $this->success(lang('exec success'), session_id());
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
        $result = $this->run()->methodAuth('upload')->auth()->send();
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
     * 验证METHOD AUTH
     * @access protected
     * @param
     * @return mixed
     */
    protected function methodAuth($_type)
    {
        if ($_type === 'handle' && !in_array($this->action, $this->handleMethod)) {
            $this->error('[METHOD] ' . $this->method . ' error');
        } elseif ($_type === 'upload' && !in_array($this->action, $this->uploadMethod)) {
            $this->error('[METHOD] ' . $this->method . ' error');
        } elseif ($_type === 'query') {
            if (in_array($this->action, $this->handleMethod) || in_array($this->action, $this->uploadMethod)) {
                $this->error('[METHOD] ' . $this->method . ' error');
            }
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
