<?php
/**
 *
 * 邮箱设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Email.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use app\common\model\Config as ModelConfig;

class Email
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getEmailConfig()
    {
        $map = [
            ['name', 'in', 'smtp_host,smtp_port,smtp_username,smtp_password,smtp_from_email,smtp_from_name'],
            ['lang', '=', 'niphp'],
        ];

        // 实例化设置表模型
        $model_config = new ModelConfig;

        $result =
        $model_config->field(true)
        ->where($map)
        ->select();

        $data = [];
        foreach ($result as $value) {
            $value = $value->toArray();
            $data[$value['name']] = $value['value'];
        }

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
            'smtp_host'       => input('post.smtp_host'),
            'smtp_port'       => input('post.smtp_port/f'),
            'smtp_username'   => input('post.smtp_username'),
            'smtp_password'   => input('post.smtp_password'),
            'smtp_from_email' => input('post.smtp_from_email'),
            'smtp_from_name'  => input('post.smtp_from_name'),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Email', 'settings', 'admin');
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
