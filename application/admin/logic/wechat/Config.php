<?php
/**
 *
 * 接口设置 - 微信 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\wechat
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\wechat;

class Config
{

    /**
     * 查询
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
            ['name', 'in', 'wechat_token,wechat_encodingaeskey,wechat_appid,wechat_appsecret'],
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
            'wechat_token'          => input('post.wechat_token'),
            'wechat_encodingaeskey' => input('post.wechat_encodingaeskey'),
            'wechat_appid'          => input('post.wechat_appid'),
            'wechat_appsecret'      => input('post.wechat_appsecret'),
            '__token__'             => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/wechat/config', $receive_data);
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
        create_action_log($lang['settings']['child']['config'], 'config_editor');

        return true;
    }
}
