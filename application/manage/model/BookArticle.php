<?php
/**
 *
 * 书库文章表 - 数据层
 *
 * @package   NiPHPCMS
 * @category  manage\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: BookArticle.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\manage\model;

use think\Model;

class BookArticle extends Model
{
    protected $name = 'book_article';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $field = [
        'id',
        'title',
        'content',
        'book_id',
        'is_pass',
        'sort',
        'hits',
        'comment_count',
        'show_time',
        'update_time',
        'delete_time',
        'create_time',
        'access_id',
    ];
}
