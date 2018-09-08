<?php
/**
 *
 * 幻灯片 - 内容 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\content
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\content;

class Banner
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $map = [
            ['pid', '=', input('param.pid/f', 0)],
            ['lang', '=', lang(':detect')],
        ];

        $result =
        model('common/banner')
        ->where($map)
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url('content/banner'),
        ]);

        foreach ($result as $key => $value) {
            $url = [
                'manage' => url('content/banner', ['operate' => 'manage', 'pid' => $value->id]),
                'remove' => url('content/banner', ['operate' => 'remove', 'id' =>  $value->id]),
            ];
            if ($pid = input('param.pid/f', 0)) {
                $url['editor'] = url('content/banner', ['operate' => 'editor', 'pid' => $pid, 'id' => $value->id]);
            } else {
                $url['editor'] = url('content/banner', ['operate' => 'editor', 'id' => $value->id]);
            }

            $result[$key]->url = $url;
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
            'name'      => input('post.name', ''),
            'pid'       => input('post.pid/f', 0),
            'title'     => input('post.title', ''),
            'width'     => input('post.width/f', 0),
            'height'    => input('post.height/f', 0),
            'image'     => input('post.image', ''),
            'url'       => input('post.url', ''),
            'lang'      => lang(':detect'),
            '__token__' => input('post.__token__'),
        ];

        if ($receive_data['pid']) {
            $result = validate('admin/content/banner.added', input('post.'));
        } else {
            $result = validate('admin/content/banner.added_main', input('post.'));
        }

        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result =
        model('common/banner')
        ->added($receive_data);

        if ($receive_data['pid']) {
            create_action_log($receive_data['title'], 'banner_image_added');
        } else {
            create_action_log($receive_data['name'], 'banner_added');
        }

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
        model('common/banner')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')],
        ])
        ->find()
        ->toArray();

        if ($result['pid']) {
            create_action_log($result['title'], 'banner_image_remove');
        } else {
            create_action_log($result['name'], 'banner_remove');
        }

        return
        model('common/banner')
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
        return
        model('common/banner')
        ->field(true)
        ->where([
            ['id', '=', input('post.id/f')]
        ])
        ->find()
        ->toArray();
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
            'id'        => input('post.id/f'),
            'name'      => input('post.name', ''),
            'pid'       => input('post.pid/f', 0),
            'title'     => input('post.title', ''),
            'width'     => input('post.width/f', 0),
            'height'    => input('post.height/f', 0),
            'image'     => input('post.image', ''),
            'url'       => input('post.url', ''),
            'lang'      => lang(':detect'),
            '__token__' => input('post.__token__'),
        ];

        if ($receive_data['pid']) {
            $result = validate('admin/content/banner.editor', input('post.'));
        } else {
            $result = validate('admin/content/banner.editor_main', input('post.'));
        }

        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result =
        model('common/banner')
        ->editor($receive_data);

        if ($receive_data['pid']) {
            create_action_log($receive_data['title'], 'banner_image_editor');
        } else {
            create_action_log($receive_data['name'], 'banner_editor');
        }

        return !!$result;
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        create_action_log('', 'banner_sort');

        return
        model('common/banner')
        ->sort([
            'id' => input('post.sort/a'),
        ]);
    }
}
