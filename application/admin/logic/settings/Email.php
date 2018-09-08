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
        $result =
        model('common/config')
        ->field(true)
        ->where([
            ['name', 'in', 'smtp_host,smtp_port,smtp_username,smtp_password,smtp_from_email,smtp_from_name'],
            ['lang', '=', 'niphp'],
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
            'smtp_host'       => input('post.smtp_host'),
            'smtp_port'       => input('post.smtp_port/f'),
            'smtp_username'   => input('post.smtp_username'),
            'smtp_password'   => input('post.smtp_password'),
            'smtp_from_email' => input('post.smtp_from_email'),
            'smtp_from_name'  => input('post.smtp_from_name'),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/settings/email', $receive_data);
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
            ->update(['
                value' => $value
            ]);
        }

        $lang = lang('__nav');
        create_action_log($lang['settings']['child']['email'], 'config_editor');

        return true;
    }
}
