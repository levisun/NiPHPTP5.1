<?php
/**
 *
 * 安全与效率设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\settings;

class Safe
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
            ['name', 'in', 'system_portal,content_check,member_login_captcha,website_submit_captcha,upload_file_max,upload_file_type,website_static'],
            ['lang', '=', 'niphp'],
        ])
        ->select()
        ->toArray();

        $admin_data = session('admin_data');
        $data = [];
        foreach ($result as $value) {
            $data[$value['name']] = $value['value'];
        }

        $data['founder'] = $admin_data['role_id'] == 1 ? 1 : 0;

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
            'content_check'          => input('post.content_check/f'),
            'member_login_captcha'   => input('post.member_login_captcha/f'),
            'website_submit_captcha' => input('post.website_submit_captcha/f'),
            'website_static'         => input('post.website_static/f'),
            'upload_file_max'        => input('post.upload_file_max/f'),
            'upload_file_type'       => input('post.upload_file_type'),
            '__token__'              => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/settings/safe', $receive_data);
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
        create_action_log($lang['settings']['child']['safe'], 'config_editor');

        return true;
    }
}
