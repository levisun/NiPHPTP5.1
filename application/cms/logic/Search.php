<?php
/**
 *
 * 搜索 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Search
{

    /**
     * 文章列表
     * @access public
     * @param  string $_q 搜索
     * @return array
     */
    public function query($_q = '')
    {
        global $map, $field, $_q;

        $_q = $_q ? $_q : input('param.q');
        $field = ['id', 'category_id', 'title', 'update_time', 'create_time'];
        $map = [
            ['title', 'like', $_q . '%'],
            ['is_pass', '=', 1],
            ['show_time', '<=', time()]
        ];

        // config('paginate.list_rows', 1);
        $paginate =
        db()->field('COUNT(*) AS tp_count')
        ->table('np_article')
        ->union(function($query){
            global $map, $field, $_q;
            $query->field('COUNT(*) AS tp_count')
            ->table('np_download')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field, $_q;
            $query->field('COUNT(*) AS tp_count')
            ->table('np_picture')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field, $_q;
            $query->field('COUNT(*) AS tp_count')
            ->table('np_product')
            ->where($map);
        })
        ->where($map)
        // ->fetchSql()
        ->paginate();

        $render = $paginate->render();
        $paginate = $paginate->toArray();

        $limit = ($paginate['current_page'] - 1) * $paginate['per_page'];
        $limit .= ', ' . $paginate['per_page'];

        $result =
        db()->field($field)
        ->table('np_article')
        ->union(function($query){
            global $map, $field, $_q;
            $query->field($field)
            ->table('np_download')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field, $_q;
            $query->field($field)
            ->table('np_picture')
            ->where($map);
        })
        ->union(function($query){
            global $map, $field, $_q;
            $query->field($field)
            ->table('np_product')
            ->where($map);
        })
        ->where($map)
        ->limit($limit)
        // ->fetchSql()
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]['flag']   = encrypt($value['id']);
            $result[$key]['title'] = htmlspecialchars_decode($value['title']);
            $result[$key]['cat_url'] = url('list/' . $value['category_id']);

            // 查询模型表名
            $table_name =
            model('common/category')
            ->view('category c', ['model_id'])
            ->view('model m', ['table_name'], 'm.id=c.model_id')
            ->where([
                ['c.id', '=', $value['category_id']],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'TABLE_NAME' . $value['category_id'] : false)
            ->value('table_name');

            $result[$key]['url'] = url($table_name . '/' . $value['category_id'] . '/' . $value['id']);

            // 查询自定义字段
            $fields =
            model('common/' . $table_name . 'Data')
            ->view($table_name . '_data d', 'data')
            ->view('fields f', ['name' => 'fields_name'], 'f.id=d.fields_id')
            ->where([
                ['d.main_id', '=', $value['id']],
            ])
            ->cache(!APP_DEBUG ? __METHOD__ . 'FIELDS' . $value['id'] : false)
            ->select()
            ->toArray();
            foreach ($fields as $val) {
                $result[$key][$val['fields_name']] = $val['data'];
            }
        }

        return [
            'list'         => $result,
            'total'        => $paginate['total'],
            'per_page'     => $paginate['per_page'],
            'current_page' => $paginate['current_page'],
            'last_page'    => $paginate['last_page'],
            'page'         => $render,
        ];
    }
}
