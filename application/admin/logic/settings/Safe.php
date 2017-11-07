<?php
/**
 *
 * 安全与效率设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Safe.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use app\common\model\Config as ModelConfig;

class Safe
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getSafeConfig()
    {
        $map = [
            ['name', 'in', 'system_portal,content_check,member_login_captcha,website_submit_captcha,upload_file_max,upload_file_type,website_static'],
            ['lang', '=', 'niphp'],
        ];

        // 实例化设置表模型
        $model_config = new ModelConfig;

        $result =
        $model_config->field(true)
        ->where($map)
        ->select();

        $admin_data = session('admin_data');
        $data = [];
        foreach ($result as $value) {
            $value = $value->toArray();
            $data[$value['name']] = $value['value'];
        }

        $data['founder'] = $admin_data['role_id'] == 1 ? 1 : 0;

        return $data;
    }

    /**
     * 修改
     * @access public
     * @param
     * @return mixed
     */
    public function update()
    {
        $form_data = [
            'content_check'          => input('post.content_check/f'),
            'member_login_captcha'   => input('post.member_login_captcha/f'),
            'website_submit_captcha' => input('post.website_submit_captcha/f'),
            'website_static'         => input('post.website_static/f'),
            'upload_file_max'        => input('post.upload_file_max/f'),
            'upload_file_type'       => input('post.upload_file_type'),
            '__token__'              => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Safe', 'settings', 'admin');
        if (true === $result) {
            unset($form_data['__token__']);

            $model_config = new ModelConfig;

            $map = $data = [];
            foreach ($form_data as $key => $value) {
                $map  = [
                    ['name', '=', $key],
                ];
                $data = ['value' => $value];

                $model_config->allowField(true)
                ->where($map)
                ->update($data);
            }

            $return = true;
        } else {
            $return = $result;
        }

        return $return;
    }
}
