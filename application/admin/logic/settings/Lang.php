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
 * @since     2017/09/13
 */
namespace app\admin\logic\settings;

use think\facade\Env;

class Lang
{

    /**
     * 查询图片设置数据
     * @access public
     * @param
     * @return array
     */
    public function getLangConfig()
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
     * 保存修改图片设置
     * @access public
     * @param  array  $form_data
     * @return mixed
     */
    public function saveLangConfig($form_data)
    {
        $config_file = Env::get('app_path') . 'admin' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $config = include($config_file);
        $config['default_lang'] = $form_data['system'];
        $config['lang_switch_on'] = $form_data['lang_switch_on'] ? true : false;
        file_put_contents($config_file, '<?php return ' . var_export($config, true) . ';');

        $config_file = Env::get('app_path') . 'cms' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $config = include($config_file);
        $config['default_lang'] = $form_data['website'];
        $config['lang_switch_on'] = $form_data['lang_switch_on'] ? true : false;
        file_put_contents($config_file, '<?php return ' . var_export($config, true) . ';');

        return true;
    }
}
