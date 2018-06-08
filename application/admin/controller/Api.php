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

class Api
{

    /**
     * 查询请求
     * @access public
     * @param
     * @return json
     */
    public function query()
    {
        $async = logic('common/async');

        $result = $async->exec();

        remove_old_upload_file(false);

        if ($result === false) {
            $output = $async->outputError(
                'request error',
                40004,
                input('param.')
            );
        } else {
            $output = $async->outputData(
                'request success',
                'SUCCESS',
                $result,
                input('param.')
            );
        }

        return $output;
    }

    /**
     * 执行请求
     * @access public
     * @param
     * @return json
     */
    public function settle()
    {
        $async = logic('common/async');

        $result = $async->exec();

        remove_old_upload_file();

        if ($result === false) {
            $output = $async->outputError(
                'data error',
                41001,  // 操作未知错误
                input('param.')
            );
        } else {
            if ($result === true) {
                $output = $async->outputData(
                    lang('save success'),
                    'SUCCESS',
                    $result,
                    input('param.')
                );
            } else {
                $output = $async->outputError(
                    $result,
                    41002,  // 操作错误
                    input('param.')
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

        $async = logic('common/async');

        $result = $async->exec();

        $json['msg']   = $result === false ? 'EMPTY' : 'SUCCESS';

        if (is_string($result)) {
            $output = $async->outputError(
                $result,
                'ERROR',
                input('param.')
            );
        } else {
            $output = $async->outputData(
                lang('upload success'),
                'SUCCESS',
                $result,
                input('param.')
            );
        }

        return $output;
    }
}
