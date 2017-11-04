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

use app\admin\logic\category\Category as LogicCategory;

use app\common\logic\Models as LogicModels;
use app\common\logic\Level as LogicLevel;

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
            'pid'  => input('param.pid/f', 0),
            'key'  => input('param.q'),
        ];

        $logic_category = new LogicCategory;
        return $logic_category->getListData($request_data);
    }

    /**
     * 新增栏目
     * @access public
     * @param
     * @return array
     */
    public function added()
    {
        if (request()->isPost()) {
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

            $return = validate($form_data, 'Category.create', 'category', 'admin');
            if (true === $return) {
                unset($form_data['__token__']);

                $logic_category = new LogicCategory;
                $return = $logic_category->create($form_data);
            }
        } else {
            $request_data = [
                'pid'  => input('param.pid/f', 0),
            ];

            $logic_category = new LogicCategory;
            $logic_models = new LogicModels;
            $logic_level = new LogicLevel;
            $return = [
                'parent' => $logic_category->getParentData($request_data),
                'type'   => $logic_category->getCategoryType(),
                'models' => $logic_models->getOpen(),
                'level'  => $logic_level->getOpen(),
            ];
        }

        return $return;
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
            $return = validate($form_data, 'Category.editor', 'category', 'admin');
            if (true === $return) {
                unset($form_data['__token__']);
                $logic_category = new LogicCategory;
                $return = $logic_category->update($form_data);
            }
        } else {
            $request_data = [
                'id'  => input('param.id/f'),
            ];

            $logic_category = new LogicCategory;
            $logic_models = new LogicModels;
            $logic_level = new LogicLevel;
            $return = [
                'data'   => $logic_category->getEditorData($request_data),
                'type'   => $logic_category->getCategoryType(),
                'models' => $logic_models->getOpen(),
                'level'  => $logic_level->getOpen(),
            ];

            if (!$return['data']) {
                $return = lang('illegal operation');
            }
        }

        return $return;
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
            'id'  => input('param.id/f'),
        ];

        $logic_category = new LogicCategory;
        return $logic_category->remove($request_data);
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

        $logic_category = new LogicCategory;
        return $logic_category->sort($form_data);
    }
}
