<?php
/**
 *
 * 管理栏目 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Category.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\admin\logic\category;

class Category
{

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function all()
    {
        $map = [
            ['c.pid', '=', input('param.pid/f', 0)],
            ['c.lang', '=', lang(':detect')],
        ];

        // 搜索
        if ($key = input('param.q')) {
            $map[] = ['c.name', 'like', $key . '%'];
        }

        $result =
        model('common/category')->view('category c', true)
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

            $url = $value->operation_url;
            if ($value->pid) {
                $url['back'] = url('');
            } else {
                $url['back'] = false;
            }

            if ($value->child) {
                $url['child'] = url('', ['pid' => $value['id']]);
            } else {
                $url['child'] = false;
            }

            $url['add_child'] = url('', ['operate' => 'added','pid' => $value['id']]);

            $result[$key]->url = $url;

        }

        return $result;
    }
}
