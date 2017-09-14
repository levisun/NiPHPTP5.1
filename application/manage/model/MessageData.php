<?php
/**
 *
 * 留言扩展表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: MessageData.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class MessageData extends Model
{
    protected $name = 'message_data';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'main_id',
        'fields_id',
        'data'
    ];
}
