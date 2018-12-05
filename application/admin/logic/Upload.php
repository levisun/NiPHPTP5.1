<?php
/**
 *
 * 上传 - 业务层
 *
 * @package   NiPHPCMS
 * @category  admin\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic;

class Upload
{

    /**
     * 上传文件
     * @access public
     * @param
     * @return mixed
     */
    public function upload()
    {
        if (request()->isPost()) {
            $receive_data = [
                'upload' => input('file.upload'),
                'type'   => input('param.type')
            ];

            // 验证请求数据
            $result = validate('admin/upload', $receive_data);
            if (true !== $result) {
                return $result;
            }

            $result =
            logic('common/upload')
            ->fileOne($receive_data);

            if (!is_string($result)) {
                create_action_log($result['save_dir'] . $result['file_name'], 'upload_file');
            }

            return $result;
        } else {
            return lang('illegal operation');
        }
    }
}
