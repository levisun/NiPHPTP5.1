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
        if (in_array(request()->module(), ['admin', 'user'])) {
            return ;
        }

        $request =
        !request()->isAjax() &&
        !request()->isPjax() &&
        !request()->isPost();

        if (!APP_DEBUG && $request) {
            // $this->isAuth();

            $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
            $html_path .= request()->module() . DIRECTORY_SEPARATOR;
            $file_name  = md5(request()->url());
            $html_path .= substr($file_name, 0, 1) . DIRECTORY_SEPARATOR;
            $html_path .= $file_name . '.html';

            if (is_file($html_path)) {
                // AJAX请求加密签名
                ajax_sign();

                include_once $html_path;
                exit();
            }
        }
    }

    public function write($_content)
    {
        if (in_array(request()->module(), ['admin', 'user'])) {
            return ;
        }

        $html_path  = env('runtime_path') . 'html' . DIRECTORY_SEPARATOR;
        $html_path .= request()->module() . DIRECTORY_SEPARATOR;

        // 开启调试删除HTML文件
        if (APP_DEBUG) {
            \File::remove($html_path);
            return ;
        } else {
            $file_name  = md5(request()->url());
            $html_path .= substr($file_name, 0, 1) . DIRECTORY_SEPARATOR;
            $html_path .= $file_name . '.html';

            $storage = new \think\template\driver\File;
            $storage->write($html_path, $_content);
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
        } elseif (request()->module() == 'user') {
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
}
