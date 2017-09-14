<?php
/**
 *
 * 评论支持表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: CommentSupport.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class CommentSupport extends Model
{
    protected $name = 'comment_support';
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
