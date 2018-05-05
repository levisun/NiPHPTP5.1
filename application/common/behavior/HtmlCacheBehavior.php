<?php
/**
 *
 * 行为
 *
 * @package   NiPHPCMS
 * @category  common\behavior
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/5
 */

namespace app\common\behavior;

class HtmlCacheBehavior
{

    public function run()
    {
        if (!APP_DEBUG && request()->controller() != 'Api') {
            $this->isAuth();

            $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
            $html_path .= request()->module() . DIRECTORY_SEPARATOR;
            $html_path .= md5(request()->url()) . '.html';

            if (is_file($html_path)) {
                include $html_path;
                exit();
            }
        }
    }

    /**
     * 权限验证
     * @access private
     * @return void
     */
    private function isAuth()
    {
        if (request()->module() == 'admin') {
            if (request()->action() != 'login') {
                session(config('session.'));
                if (!session('?' . config('user_auth_key'))) {
                    $this->redirect(url('account/login'));
                }
            } else {
                session(config('session.'));
                if (session('?' . config('user_auth_key'))) {
                    $this->redirect(url('settings/info'));
                }
            }
        } elseif (request()->module() == 'cms') {
            # code...
        }
    }

    /**
     * 重定向
     * @access private
     * @param  string $_url
     * @return void
     */
    private function redirect($_url)
    {
        header('Location:' . $_url);
        exit();
    }

    public function write($_content)
    {
        $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
        $html_path .= request()->module() . DIRECTORY_SEPARATOR;
        $html_path .= md5(request()->url()) . '.html';

        $storage = new \think\template\driver\File;
        $storage->write($html_path, $_content);
    }
}
