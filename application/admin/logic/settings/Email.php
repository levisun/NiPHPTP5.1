<?php
/**
 *
 * 邮箱设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\settings;

class Email
{

    /**
     * 查询基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $map = [
            ['name', 'in', 'smtp_host,smtp_port,smtp_username,smtp_password,smtp_from_email,smtp_from_name'],
            ['lang', '=', 'niphp'],
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
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'smtp_host'       => input('post.smtp_host'),
            'smtp_port'       => input('post.smtp_port/f'),
            'smtp_username'   => input('post.smtp_username'),
            'smtp_password'   => input('post.smtp_password'),
            'smtp_from_email' => input('post.smtp_from_email'),
            'smtp_from_name'  => input('post.smtp_from_name'),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/email', $receive_data, 'settings');
        if (true !== $result) {
            return $result;
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

        $lang = lang('_menu');
        create_action_log($lang['settings_email'], 'config_editor');

        return true;
    }
}
