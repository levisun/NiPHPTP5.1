<?php
/**
 *
 * 关键词 - 微信 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\wechat
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\wechat;

class Keyword
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query()
    {
        $type = input('post.type/f', 0);

        $map = [
            ['type', '=', $type],
            ['lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['keyword', 'like', $key . '%'];
        }

        // 根据类型判断URL
        if ($type == 0) {
            $url = 'wechat/keyword';
        } elseif ($type == 1) {
            $url = 'wechat/auto';
        } else {
            $url = 'wechat/attention';
        }

        $result =
        model('common/reply')->field(true)
        ->order('id DESC')
        ->paginate(null, null, [
            'path' => url($url),
        ]);

        foreach ($result as $key => $value) {
            $result[$key]->type_name = $value->type_name;
            $result[$key]->status = $value->status_name;
            $result[$key]->url = [
                'editor' => url($url, ['operate' => 'editor', 'id' => $value['id']]),
                'remove' => url($url, ['operate' => 'remove', 'id' => $value['id']]),
            ];
        }
        $page = $result->render();
        $list = $result->toArray();

        return [
            'list' => $list['data'],
            'page' => $page
        ];
    }

    /**
     * 新增关键词
     * @access public
     * @param
     * @return mixed
     */
    public function added()
    {
        $receive_data = [
            'keyword'   => input('post.keyword'),
            'title'     => input('post.title'),
            'content'   => input('post.content'),
            'image'     => input('post.image'),
            'url'       => input('post.url'),
            'status'    => input('post.status/f', 0),
            'type'      => input('post.type/f', 0),
            'lang'      => lang(':detect'),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/keyword.added', input('post.'), 'wechat');
        if (true !== $result) {
            return $result;
        }

        unset($receive_data['__token__']);

        $result = model('common/reply')
        ->added($receive_data);

        create_action_log($receive_data['keyword'], 'wechat_keyword_added');

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
        $map  = [
            ['id', '=', input('post.id/f')],
        ];

        $result = $this->find();

        // 删除图片
        \File::remove(env('root_path') . basename(request()->root()) . $result['image']);

        create_action_log($result['keyword'], 'wechat_keyword_remove');

        $receive_data = [
            'id' => input('post.id/f'),
        ];
        return model('common/reply')
        ->remove($receive_data);
    }

    /**
     * 查询要修改的数据
     * @access public
     * @param
     * @return array
     */
    public function find()
    {
        $map = [
            ['id', '=', input('post.id/f')]
        ];

        return model('common/reply')->field(true)
        ->where($map)
        ->find();
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
            'keyword'   => input('post.keyword'),
            'title'     => input('post.title'),
            'content'   => input('post.content'),
            'image'     => input('post.image'),
            'url'       => input('post.url'),
            'status'    => input('post.status/f', 0),
            'type'      => input('post.type/f', 0),
            'lang'      => lang(':detect'),
            '__token__' => input('post.__token__'),
        ];

        $result = validate('admin/keyword.editor', input('post.'), 'wechat');
        if (true !== $result) {
            return $result;
        }

        create_action_log($receive_data['keyword'], 'wechat_keyword_editor');

        return model('common/reply')
        ->editor($receive_data);
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $receive_data = [
            'id' => input('post.sort/a'),
        ];

        create_action_log('', 'wechat_keyword_sort');

        return model('common/reply')
        ->sort($receive_data);
    }
}
