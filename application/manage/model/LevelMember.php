<?php
/**
 *
 * 会员组关系表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: LevelMember.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class LevelMember extends Model
{
    protected $name = 'level_member';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'user_id';
    protected $field = [
        'user_id',
        'level_id',
    ];
}
