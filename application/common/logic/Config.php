<?php
/**
 *
 * 设置 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Config.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\Config as ModelConfig;

class Config
{

    /**
     * 保存设置
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function update($_form_data)
    {
        // 实例化设置表模型
        $config = new ModelConfig;

        $map = $data = [];
        foreach ($_form_data as $key => $value) {
            $map  = [
                ['name', '=', $key],
            ];
            $data = ['value' => $value];

            $config->allowField(true)
            ->where($map)
            ->update($data);
        }

        return true;
    }
}
