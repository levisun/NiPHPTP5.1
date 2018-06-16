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
        $result = $this->analysis();
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
        $result = $this->analysis();
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
        $_POST['method'] = 'upload.file';

        $result = $this->analysis();
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
}
