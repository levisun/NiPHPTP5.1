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

class Category
{

    /**
     * 新增栏目
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function create($_form_data)
    {
        $category = new ModelCategory;
        $result =
        $category->allowField(true)
        ->create($_form_data);

        return $result->id;
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
     * @param  array  $_form_data
     * @return boolean
     */
    public function update($_form_data)
    {
        $map  = [
            ['id', '=', $_form_data['id']],
        ];

        unset($_form_data['id']);

        $category = new ModelCategory;
        $result =
        $category->allowField(true)
        ->where($map)
        ->update($_form_data);

        return !!$result;
    }

    /**
     * 排序
     * @access public
     * @param  array $_form_data
     * @return boolean
     */
    public function sort($_form_data)
    {
        foreach ($_form_data['id'] as $key => $value) {
            $data[] = [
                'id' => $key,
                'sort' => $value,
            ];
        }

        $category = new ModelCategory;
        $result =
        $category->saveAll($data);

        return !!$result;
    }
}
