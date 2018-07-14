<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\controller;

use app\common\logic\Async;

class Api extends Async
{

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        return parent::query();
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        remove_old_upload_file();

        return parent::settle();
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        $result = $this->examine();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        $json['msg'] = $result === false ? 'EMPTY' : 'SUCCESS';

        if (is_string($result)) {
            return $this->outputError(
                $result,
                'ERROR'
            );
        } else {
            return $this->outputData(
                lang('upload success'),
                $result
            );
        }
    }

    protected function examine()
    {
        if (!$error = parent::examine()) {
            return $error;
        }

        // 权限验证
        if ($this->action != 'login') {
            // 是否登录
            if (!session('?' . config('user_auth_key'))) {
                return $this->outputError(
                    'ILLEGAL REQUEST1',
                    'ERROR'
                );
            }

            // 登录权限信息
            if (!session('?_access_list')) {
                return $this->outputError(
                    'ILLEGAL REQUEST',
                    'ERROR'
                );
            }

            // 是否有访问操作等权限
            $access_list = session('_access_list');
            $access_list = $access_list['ADMIN'];
            if (!in_array($this->class, ['login', 'logout']) && empty($access_list[strtoupper($this->layer)][strtoupper($this->class)])) {
                return $this->outputError(
                    'ILLEGAL REQUEST',
                    'ERROR'
                );
            }
        }

        if ($this->class == 'upload') {
            $this->layer = 'logic';
        }

        return true;
    }
}
