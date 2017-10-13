<?php
/**
 *
 * 语言设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Lang.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Lang
{

    /**
     * 获得图片设置数据
     * @access public
     * @param
     * @return array
     */
    public function getLangConfig()
    {
        $basic = logic('Lang', 'logic\settings');
        return $basic->getLangConfig();
    }

    /**
     * 保存图片基础设置
     * @access public
     * @param
     * @return mixed
     */
    public function saveLangConfig()
    {
        $form_data = [
            'system'         => request()->post('system'),
            'website'        => request()->post('website'),
            'lang_switch_on' => request()->post('lang_switch_on/f'),
            '__token__'      => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Lang', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Lang', 'logic\settings');
        return $basic->saveLangConfig($form_data);
    }
}
