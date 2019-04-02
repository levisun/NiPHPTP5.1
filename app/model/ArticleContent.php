<?php
/**
 *
 * 数据层
 * 文章表
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

class ArticleContent extends Model
{
    protected $name = 'article_content';
    // protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    // protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $type = [
        'article_id' => 'integer',
    ];
    protected $field = [
        'id',
        'article_id',
        'content',
        'thumb'
    ];
}
