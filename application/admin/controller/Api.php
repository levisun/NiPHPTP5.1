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
 * @since     2018/9
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
        $result = $this->exec();
        if ($result === false) {
            return $this->outputError($this->errorMsg);
        } else {
            return $this->outputData('QUERY SUCCESS', $result);
        }
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        $result = $this->exec();
        if ($result === false && $this->errorMsg) {
            return $this->outputError($this->errorMsg);
        } elseif ($result === true) {
            return $this->outputData(lang('exec success'), $result);
        } else {
            return $this->outputError($result);
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
        $result = $this->exec();
        if ($result === false && $this->errorMsg) {
            return $this->outputError($this->errorMsg);
        } elseif (is_string($result)) {
            return $this->outputError($result);
        } else {
            return $this->outputData(lang('upload success'), $result);
        }
    }


    /**
     * 验证Auth
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkAuth()
    {
        // 权限验证
        if ($this->action != 'login') {
            // 是否登录
            if (!session('?' . config('user_auth_key'))) {
                return 'ILLEGAL REQUEST';
            }

            // 过滤基础信息查询方法权限判断
            if (!in_array($this->action, ['added', 'reomve', 'find', 'editor', 'query', 'upload'])) {
                return true;
            }

            $auth =
            !logic('common/Rbac')
            ->checkAuth(
                session(config('user_auth_key')),
                'admin',
                $this->layer,
                $this->class,
                $this->action
            );

            // 登录权限信息
            if ($auth) {
                return 'ILLEGAL REQUEST';
            }

            if ($this->action === 'upload') {
                $this->layer  = 'logic';
                $this->class  = 'upload';
                $this->action = 'file';
            }
        }

        return true;
    }

    /**
     * 验证异步加密签名
     * @access protected
     * @param
     * @return mixed
     */
    protected function checkSign()
    {
        return true;
    }
}
