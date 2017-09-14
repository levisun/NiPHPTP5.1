<?php
/**
 *
 * 管理员组关系表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: RoleAdmin.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class RoleAdmin extends Model
{
    protected $name = 'role_admin';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'user_id';
    protected $field = [
        'user_id',
        'role_id',
    ];
}
