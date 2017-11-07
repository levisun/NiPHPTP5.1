<?php
/**
 *
 * 基础设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Basic.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use app\common\model\Config as ModelConfig;

class Basic
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getBasicConfig()
    {
        $map = [
            ['name', 'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script'],
            ['lang', '=', lang(':detect')],
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
            'website_name'        => input('post.website_name'),
            'website_keywords'    => input('post.website_keywords'),
            'website_description' => input('post.website_description'),
            'bottom_message'      => input('post.bottom_message', '', 'trim,htmlspecialchars'),
            'copyright'           => input('post.copyright'),
            'script'              => input('post.script', '', 'trim,htmlspecialchars'),
            '__token__'           => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Basic', 'settings', 'admin');
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
