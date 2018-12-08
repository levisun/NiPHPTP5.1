<?php
/**
 *
 * 基础设置 - 设置 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
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
    public function query()
    {
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script'],
            ['lang', '=', lang(':detect')],
        ])
        ->select()
        ->toArray();

        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = $value['value'];
        }

        return $data;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'website_name'        => input('post.website_name'),
            'website_keywords'    => input('post.website_keywords'),
            'website_description' => input('post.website_description'),
            'bottom_message'      => input('post.bottom_message', '', config('content_filter')),
            'copyright'           => input('post.copyright'),
            'script'              => input('post.script', '', 'trim,htmlspecialchars'),
            '__token__'           => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/settings/basic', $receive_data);
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $model_config = model('common/config');

        $map = $data = [];
        foreach ($receive_data as $key => $value) {
            $model_config
            ->allowField(true)
            ->where([
                ['name', '=', $key],
            ])
            ->update([
                'value' => $value
            ]);
        }

        $lang = lang('__nav');
        create_action_log($lang['settings']['child']['basic'], 'config_editor');

        return true;
    }
}
