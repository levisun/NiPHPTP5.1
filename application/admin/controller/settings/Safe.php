<?php
/**
 *
 * 安全与效率设置 - 设置 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\settings
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Safe.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\settings;

class Safe
{

    /**
     * 获得基础设置数据
     * @access public
     * @param
     * @return array
     */
    public function getSafeConfig()
    {
        $basic = logic('Safe', 'logic\settings');
        return $basic->getSafeConfig();
    }

    /**
     * 保存修改基础设置
     * @access public
     * @param  array  $form_data
     * @return mixed
     */
    public function saveSafeConfig($form_data)
    {
        // 验证请求数据
        $result = validate($form_data, 'Safe', 'validate\settings');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $basic = logic('Safe', 'logic\settings');
        return $basic->saveSafeConfig($form_data);
    }
}
