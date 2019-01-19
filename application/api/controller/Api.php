<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHP
 * @category  application\api\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\api\controller;

use app\common\logic\Async;

class Api extends Async
{

    public function index()
    {
        list($model, $action) = explode('/', request()->path(), 2);

        $logic = logic('api/' . $model);

        call_user_func_array([$logic, $action], []);
    }

    /**
     * 错误页面
     * @access public
     * @param
     * @return
     */
    public function abort()
    {
        abort(404);
    }

    /**
     * 获得IP地址地区信息
     * @access public
     * @param
     * @return json
     */
    public function getipinfo()
    {
        $result = logic('common/IpInfo')->getInfo(input('param.ip'));
        $this->success('QUERY SUCCESS', $result);
    }

    /**
     * 访问记录
     * @access public
     * @param
     * @return void
     */
    public function visit()
    {
        $visit = new \app\common\middleware\Visit;
        $visit->addedVisit();
        $visit->addedSearchengine();
        $visit->createSitemap();
        die();
    }

    /**
     * 上传
     * @access public
     * @param
     * @return json
     */
    public function upload()
    {
        $result = $this->run()->token()->methodAuth('upload')->auth()->send();
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
}
