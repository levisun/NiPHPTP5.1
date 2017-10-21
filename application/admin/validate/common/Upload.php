<?php
/**
 *
 * 上传 - 验证器
 *
 * @package   NiPHPCMS
 * @category  admin\validate
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Upload.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\validate\common;

use think\Validate;

class Upload extends Validate
{
    protected $rule = [
        'upload'   => ['require'],
        'type'     => ['require'],
        // 'model'    => ['require'],
        'input_id' => ['require'],
    ];

    protected $message = [
        'upload.require'   => '{%error upload file require}',
        'type.require'     => '{%error upload type require}',
        // 'model.require'    => '{%error upload model require}',
        'input_id.require' => '{%error upload input_id require}',
    ];
}
