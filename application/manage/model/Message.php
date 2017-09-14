<?php
/**
 *
 * 留言表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Message.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;
use think\model\concern\SoftDelete

class Message extends Model
{
    use SoftDelete;
    protected $name = 'message';
    protected $autoWriteTimestamp = true;
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    protected $field = [
        'id',
        'title',
        'username',
        'content',
        'reply',
        'category_id',
        'type_id',
        'mebmer_id',
        'is_pass',
        'create_time',
        'update_time',
        'delete_time',
        'lang'
    ];
}
