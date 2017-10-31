<?php
/**
 *
 * 栏目 - 业务层
 *
 * @package   NiPHPCMS
 * @category  common\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\common\logic;

use app\common\model\Category as ModelCategory;
use app\common\validate\Category as ValiCategory;

class Category
{

    /**
     * 新增栏目
     * @access public
     * @param
     * @return mixed
     */
    public function create()
    {
        $form_data = [
            'name'            => input('post.name'),
            'aliases'         => input('post.aliases'),
            'pid'             => input('post.pid/f', 0),
            'type_id'         => input('post.type_id/f', 1),
            'model_id'        => input('post.model_id/f', 1),
            'is_show'         => input('post.is_show/f', 1),
            'is_channel'      => input('post.is_channel/f', 0),
            'image'           => input('post.image'),
            'seo_title'       => input('post.seo_title'),
            'seo_keywords'    => input('post.seo_keywords'),
            'seo_description' => input('post.seo_description'),
            'access_id'       => input('post.access_id/f', 0),
            '__token__'       => input('post.__token__'),
        ];

        $validate = new ValiCategory;
        $result = $validate->scene('create')->check($form_data);

        if ($result) {
            unset($form_data['__token__']);

            $category = new ModelCategory;
            $result =
            $category->allowField(true)
            ->create($form_data);

            $return = $result->id;
        } else {
            $return = $validate->getError();
        }

        return $return;
    }

    /**
     * 删除栏目
     * @access public
     * @param  array  $_receive_data
     * @return boolean
     */
    public function remove($_receive_data)
    {
        $map  = [
            ['id', '=', $_receive_data['id']],
        ];

        $category = new ModelCategory;
        $result =
        $category->where($map)
        ->delete();

        return !!$result;
    }

    /**
     * 保存修改栏目
     * @access public
     * @param
     * @return boolean
     */
    public function update()
    {
        $form_data = [
            'id'              => input('post.id/f'),
            'name'            => input('post.name'),
            'aliases'         => input('post.aliases'),
            'pid'             => input('post.pid/f', 0),
            'type_id'         => input('post.type_id/f', 1),
            'model_id'        => input('post.model_id/f', 1),
            'is_show'         => input('post.is_show/f', 1),
            'is_channel'      => input('post.is_channel/f', 0),
            'image'           => input('post.image'),
            'seo_title'       => input('post.seo_title'),
            'seo_keywords'    => input('post.seo_keywords'),
            'seo_description' => input('post.seo_description'),
            'access_id'       => input('post.access_id/f', 0),
            '__token__'       => input('post.__token__'),
        ];

        $validate = new ValiCategory;
        $result = $validate->scene('update')->check($form_data);

        if ($result) {
            $map  = [
                ['id', '=', $form_data['id']],
            ];

            unset($form_data['id']);

            $category = new ModelCategory;
            $result =
            $category->allowField(true)
            ->where($map)
            ->update($form_data);

            $return = !!$result;
        } else {
            $return = $validate->getError();
        }

        return $return;
    }

    /**
     * 排序
     * @access public
     * @param
     * @return boolean
     */
    public function sort()
    {
        $form_data = [
            'id' => input('post.sort/a'),
        ];

        foreach ($form_data['id'] as $key => $value) {
            $data[] = [
                'id'   => $key,
                'sort' => $value,
            ];
        }

        $category = new ModelCategory;
        $result =
        $category->saveAll($data);

        return !!$result;
    }
}
