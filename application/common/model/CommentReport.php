<?php
/**
 *
 * 评论举报表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: CommentReport.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\model;

use think\Model;

class CommentReport extends Model
{
    protected $name = 'comment_report';
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'create_time',
        'comment_id',
        'user_id',
        'ip',
        'ip_attr'
    ];
}
