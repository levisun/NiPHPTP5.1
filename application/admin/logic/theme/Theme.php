<?php
/**
 *
 * 管理主题 - 界面 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\theme
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\theme;

class Theme
{

    /**
     * 查询
     * @access public
     * @param
     * @return mixed
     */
    public function query()
    {
        $default_theme =
        model('common/config')
        ->field(true)
        ->where([
            ['name', '=', input('post.type') . '_theme'],
            ['lang', '=', lang(':detect')],
        ])
        ->value('value');

        $dir = env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR;
        $data = (array) glob($dir . input('post.type') . DIRECTORY_SEPARATOR . '*');

        $domain = request()->domain() . request()->root() . '/theme/' . input('post.type') . '/';

        $result = [];
        foreach ($data as $key => $value) {
            if (is_file($value . DIRECTORY_SEPARATOR . 'view.jpg')) {
                $result[$key]['img'] = $domain . end($value) . 'view.jpg';
            } else {
                $result[$key]['img'] = request()->domain() . request()->root() . '/static/images/view.jpg';
            }

            $value = explode(DIRECTORY_SEPARATOR, $value);
            $result[$key]['name'] = end($value);
        }

        return ['list' => $result, 'default_theme' => $default_theme];
    }

    public function editor()
    {
        $result =
        model('common/config')
        ->allowField(true)
        ->where([
            ['name', '=', input('post.type') . '_theme'],
        ])
        ->update([
            'value' => input('post.name')
        ]);

        $lang = lang('__nav');
        create_action_log($lang['theme']['child'][input('post.type')], 'config_editor');

        return true;
    }
}
