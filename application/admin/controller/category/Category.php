<?php
/**
 *
 * 管理栏目 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\controller\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\controller\category;

// use think\Controller;

class Category
{

    /**
     * 获得列表数据
     * @access public
     * @param
     * @return array
     */
    public function getListData()
    {
        $request_data = [
            'pid'  => request()->param('pid/f', 0),
            'key'  => request()->param('q'),
        ];

        $category = logic('Category', 'logic\category');
        return $category->getListData($request_data);
    }

    /**
     * 获得添加所需数据
     * @access public
     * @param
     * @return array
     */
    public function added()
    {
        if (request()->isPost()) {
            $result = $this->create();
        } else {
            $request_data = [
                'pid'  => request()->param('pid/f', 0),
            ];

            $category = logic('Category', 'logic\category');
            $result = [
                'parent' => $category->getParentData($request_data),
                'type'   => $category->getCategoryType(),
                'models' => $category->getCategoryModels(),
                'level'  => $category->getMemberLevel(),
            ];
        }

        return $result;
    }

    /**
     * 新增栏目
     * @access private
     * @param
     * @return mixed
     */
    private function create()
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
            'access_id'       => input('post.access_id', 0),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Category.create', 'validate\category');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $category = logic('Category', 'logic\category');
        return $category->create($form_data);
    }

    /**
     * 编辑栏目
     * @access public
     * @param
     * @return array
     */
    public function editor()
    {
        if (request()->isPost()) {
            $result = $this->update();
        } else {
            $request_data = [
                'id'  => request()->param('id/f'),
            ];

            $category = logic('Category', 'logic\category');
            $result = [
                'category' => $category->getEditorData($request_data),
                'type'     => $category->getCategoryType(),
                'models'   => $category->getCategoryModels(),
                'level'    => $category->getMemberLevel(),
            ];
        }

        return $result;
    }

    /**
     * 保存修改栏目
     * @access private
     * @param
     * @return mixed
     */
    private function update()
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
            'access_id'       => input('post.access_id', 0),
            '__token__'       => input('post.__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Category.update', 'validate\category');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $category = logic('Category', 'logic\category');
        return $category->update($form_data);
    }

    /**
     * 删除栏目
     * @access public
     * @param
     * @return mixed
     */
    public function remove()
    {
        $request_data = [
            'id'  => request()->param('id/f'),
        ];

        $category = logic('Category', 'logic\category');
        return $category->remove($request_data);
    }
}
