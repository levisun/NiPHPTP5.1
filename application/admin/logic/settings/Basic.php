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

class Basic
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getData()
    {
        $map = [
            ['name', 'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script'],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/config')->field(true)
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
        $receive_data = [
            'website_name'        => input('post.website_name'),
            'website_keywords'    => input('post.website_keywords'),
            'website_description' => input('post.website_description'),
            'bottom_message'      => input('post.bottom_message', '', 'trim,escape_xss,htmlspecialchars'),
            'copyright'           => input('post.copyright'),
            'script'              => input('post.script', '', 'trim,htmlspecialchars'),
            '__token__'           => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/basic', $receive_data, 'settings');
        if (true !== $result) {
            return backData($result, 'ERROR');
        }

        unset($receive_data['__token__']);

        $model_config = model('common/config');

        $map = $data = [];
        foreach ($receive_data as $key => $value) {
            $map  = [
                ['name', '=', $key],
            ];
            $data = ['value' => $value];

            $model_config->allowField(true)
            ->where($map)
            ->update($data);
        }

        return backData(lang('save success'), 'SUCCESS');
    }
}
