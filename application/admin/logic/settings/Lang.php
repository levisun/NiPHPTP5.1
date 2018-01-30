<?php
/**
 *
 * 语言设置 - 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Lang.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\settings;

use think\facade\Env;

class Lang
{

    /**
     * 查询语言设置数据
     * @access public
     * @param
     * @return array
     */
    public function getData()
    {
        $data = [
            'lang_switch_on'   => config('lang_switch_on') ? 1 : 0,
            'lang_list'        => config('lang_list'),
            'sys_default_lang' => config('default_lang'),
        ];

        $config = include(Env::get('app_path') . 'cms' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php');
        $data['web_default_lang'] = $config['default_lang'];

        return $data;
    }

    /**
     * 保存修改语言设置
     * @access public
     * @param
     * @return mixed
     */
    public function update()
    {
        $receive_data = [
            'system'         => input('post.system'),
            'website'        => input('post.website'),
            'lang_switch_on' => input('post.lang_switch_on'),
            '__token__'      => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate('admin/lang', $receive_data, 'settings');
        if (true !== $result) {
            return $result;
        }

        $config_file = Env::get('app_path') . 'admin' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $config = include($config_file);
        $config['default_lang'] = $receive_data['system'];
        $config['lang_switch_on'] = $receive_data['lang_switch_on'] ? true : false;
        file_put_contents($config_file, '<?php return ' . var_export($config, true) . ';');

        $config_file = Env::get('app_path') . 'cms' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $config = include($config_file);
        $config['default_lang'] = $receive_data['website'];
        $config['lang_switch_on'] = $receive_data['lang_switch_on'] ? true : false;
        file_put_contents($config_file, '<?php return ' . var_export($config, true) . ';');

        return true;
    }
}
