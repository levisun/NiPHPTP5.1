<?php
/**
 *
 * 数据层
 * 标签文章关联表
 *
 * @package   NICMS
 * @category  app\model
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\model;

use think\Model;

class TagsArticle extends Model
{
    protected $name = 'tags_article';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'tags_id'     => 'integer',
        'article_id'  => 'integer',
    ];
    protected $field = [
        'id',
        'tags_id',
        'article_id',
    ];
}
