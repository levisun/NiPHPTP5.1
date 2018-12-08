<?php
/**
 *
 * 标签文章关联表 - 数据层
 *
 * @package   NiPHP
 * @category  application\common\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\common\model;

use think\Model;

class TagsArticle extends Model
{
    protected $name = 'tags_article';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'tags_id'     => 'integer',
        'category_id' => 'integer',
        'article_id'  => 'integer',
    ];
    protected $field = [
        'id',
        'tags_id',
        'category_id',
        'article_id',
    ];
}
