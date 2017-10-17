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
    public function addedCategory()
    {
        if (request()->isPost()) {
            # code...
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
     * 编辑栏目
     * @access public
     * @param
     * @return array
     */
    public function editorCategory()
    {
        if (request()->isPost()) {
            $result = $this->saveCategory();
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
     * @param  array   $form_data
     * @return mixed
     */
    private function saveCategory()
    {
        $form_data = [
            'id'              => request()->post('id/f'),
            'name'            => request()->post('name'),
            'aliases'         => request()->post('aliases'),
            'pid'             => request()->post('pid/f', 0),
            'type_id'         => request()->post('type_id/f', 1),
            'model_id'        => request()->post('model_id/f', 1),
            'is_show'         => request()->post('is_show/f', 1),
            'is_channel'      => request()->post('is_channel/f', 0),
            'image'           => request()->post('image'),
            'seo_title'       => request()->post('seo_title'),
            'seo_keywords'    => request()->post('seo_keywords'),
            'seo_description' => request()->post('seo_description'),
            'access_id'       => request()->post('access_id', 0),
            '__token__'       => request()->post('__token__'),
        ];

        // 验证请求数据
        $result = validate($form_data, 'Category.editor', 'validate\category');
        if (true !== $result) {
            return $result;
        }

        unset($form_data['__token__']);

        $category = logic('Category', 'logic\category');
        return $category->saveCategory($form_data);
    }
}
