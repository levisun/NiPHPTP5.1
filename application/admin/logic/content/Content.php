<?php
/**
 *
 * 内容 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/7
 */
namespace app\admin\logic\content;

class Content
{

    /**
     * 内容类别
     * @access public
     * @param
     * @return array
     */
    public function category()
    {
        $map = [
            ['c.pid', '=', input('param.pid/f', 0)],
            ['c.lang', '=', lang(':detect')],
        ];

        $result =
        model('common/category')
        ->view('category c', ['id', 'name', 'type_id', 'is_show', 'is_channel', 'model_id'])
        ->view('model m', ['name' => 'model_name'], 'm.id=c.model_id')
        ->view('category cc', ['id' => 'child'], 'c.id=cc.pid', 'LEFT')
        ->where($map)
        ->group('c.id')
        ->order('c.sort DESC, c.id DESC')
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->type_name = $value->type_name;
            $result[$key]->show      = $value->show;
            $result[$key]->channel   = $value->channel;

            $url = [];

            if ($value->child) {
                $url['child'] = url('content/content', array('operate' => 'child', 'cat_id' => $value->id));
            }

            if ($value->model_id == 4) {
                $url['manage'] = url('content/content', array('operate' => 'page', 'cat_id' => $value->id));
            } else {
                $url['manage'] = url('content/content', array('operate' => 'manage', 'cat_id' => $value->id));
            }

            $result[$key]->url = $url;
        }

        return $result->toArray();
    }

    public function query()
    {
        # code...
    }
}
