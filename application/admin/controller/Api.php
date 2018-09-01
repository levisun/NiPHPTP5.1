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
        $result = $this->init();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        return $this->outputData(
            'QUERY SUCCESS',
            $result
        );
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        $result = $this->init();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        $json['msg'] = $result === false ? 'EMPTY' : 'SUCCESS';

        if (is_string($result)) {
            return $this->outputError($result);
        } else {
            return $this->outputData(
                lang('upload success'),
                $result
            );
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
        $result = $this->init();
        if ($result !== true) {
            return $result;
        }

        $result = $this->exec();

        if ($result === true) {
            return $this->outputData(
                lang('exec success'),
                $result
            );
        } elseif ($result === false) {
            return $this->outputError('data error');
        } else {
            return $this->outputError($result);
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

            // 登录权限信息
            if (!session('?_access_list')) {
                return 'ILLEGAL REQUEST';
            }

            // 是否有访问操作等权限
            $access_list = session('_access_list');
            $access_list = $access_list['ADMIN'];
            // 添加界面权限
            $access_list['THEME']['THEME'] = true;

            if (!in_array($this->class, ['login', 'logout']) && empty($access_list[strtoupper($this->layer)][strtoupper($this->class)])) {
                return 'ILLEGAL REQUEST';
            }
        }

        if ($this->class == 'upload') {
            $this->layer = 'logic';
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
