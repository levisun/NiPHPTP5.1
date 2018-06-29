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
        $result = $this->examine();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        return $this->outputData(
            lang('query success'),
            'SUCCESS',
            $result
        );
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        $result = $this->examine();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        remove_old_upload_file();

        if ($result === false) {
            $output = $this->outputError(
                'data error',
                41001
            );
        } else {
            if ($result === true) {
                $output = $this->outputData(
                    lang('save success'),
                    'SUCCESS',
                    $result
                );
            } else {
                $output = $this->outputError(
                    $result,
                    41002
                );
            }
        }

        return $output;
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

        $json['msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

        if (is_string($result)) {
            $output = $this->outputError(
                $result,
                'ERROR'
            );
        } else {
            $output = $this->outputData(
                lang('upload success'),
                'SUCCESS',
                $result
            );
        }

        return $output;
    }

    protected function examine()
    {
        $result = $this->analysis();
        if ($result !== true) {
            return $result;
        }

        // 权限验证
        if ($this->action != 'login') {
            // 是否登录
            if (!session('?' . config('user_auth_key'))) {
                return $this->outputError(
                    'ILLEGAL REQUEST ',
                    'ERROR'
                );
            }

            // 登录权限信息
            if (!session('?_access_list')) {
                return $this->outputError(
                    'ILLEGAL REQUEST  ',
                    'ERROR'
                );
            }

            // 是否有访问操作等权限
            $access_list = session('_access_list');
            $access_list = $access_list['ADMIN'];
            if (empty($access_list[strtoupper($this->layer)][strtoupper($this->class)])) {
                return $this->outputError(
                    'ILLEGAL REQUEST   ',
                    'ERROR'
                );
            }
        }

        if ($this->class == 'upload') {
            $this->layer = 'logic';
        }

        $result = parent::examine();
        if ($result !== true) {
            return $result;
        }

        return true;
    }
}
