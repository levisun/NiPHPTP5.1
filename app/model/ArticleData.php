<?php
/**
 *
 * 数据层
 * 文章附加字段表
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

class ArticleData extends Model
{
    protected $name = 'article_data';
    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    protected $pk = 'id';
    protected $type = [
        'main_id'   => 'integer',
        'fields_id' => 'integer',
    ];
    protected $field = [
        'id',
        'main_id',
        'fields_id',
        'data'
    ];
}
