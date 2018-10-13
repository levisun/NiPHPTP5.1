<?php
/**
 *
 * 广告 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/6
 */
namespace app\admin\logic\content;

class Ads
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $result =
        model('common/ads')
        ->field(true)
        ->where([
            ['lang', '=', lang(':detect')],
        ])
        ->order('id DESC')
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->start_time = date('Y/m/d', $value['start_time']);
            $result[$key]->end_time = date('Y/m/d', $value['end_time']);
            $result[$key]->url = [
                'editor' => url('content/ads', ['operate' => 'editor', 'id' => $value->id]),
                'remove' => url('content/ads', ['operate' => 'remove', 'id' => $value->id]),
            ];
        }

        $list = $result->toArray();

        return [
            'list'         => $list['data'],
            'total'        => $list['total'],
            'per_page'     => $list['per_page'],
            'current_page' => $list['current_page'],
            'last_page'    => $list['last_page'],
            'page'         => $result->render(),
        ];
    }

    /**
     * 新增
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'name'       => input('post.name'),
            'width'      => input('post.width/d'),
            'height'     => input('post.height/d'),
            'image'      => input('post.image'),
            'url'        => input('post.url'),
            'start_time' => input('post.start_time/f', 0, 'trim,strtotime'),
            'end_time'   => input('post.end_time/f', 0, 'trim,strtotime'),
            'lang'       => lang(':detect'),
            '__token__'  => input('post.__token__'),
        ];

        $result = validate('admin/content/ads.added', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result =
        model('common/ads')
        ->added($receive_data);

        create_action_log($receive_data['name'], 'ads_added');

        return !!$result;
    }

    /**
     * 删除
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {
        $result =
        model('common/ads')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')],
        ])
        ->find()
        ->toArray();

        create_action_log($result['name'], 'ads_remove');

        return
        model('common/ads')
        ->remove([
            'id' => input('post.id/f'),
        ]);
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {
        $result =
        model('common/ads')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();

        $result['start_time'] = date('Y-m-d', $result['start_time']);
        $result['end_time'] = date('Y-m-d', $result['end_time']);

        return $result;
    }

    /**
     * 编辑
     * @access public
     * @param
     * @return mixed
     */
    public function editor()
    {
        $receive_data = [
            'id'         => input('post.id/f'),
            'name'       => input('post.name'),
            'width'      => input('post.width/d'),
            'height'     => input('post.height/d'),
            'image'      => input('post.image'),
            'url'        => input('post.url'),
            'start_time' => input('post.start_time/f', 0, 'trim,strtotime'),
            'end_time'   => input('post.end_time/f', 0, 'trim,strtotime'),
            'lang'       => lang(':detect'),
            '__token__'  => input('post.__token__'),
        ];

        $result = validate('admin/content/ads.editor', input('post.'));
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result =
        model('common/ads')
        ->editor($receive_data);

        create_action_log($receive_data['name'], 'ads_editor');

        return !!$result;
    }
}
