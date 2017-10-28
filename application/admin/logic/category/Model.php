<?php
/**
 *
 * 管理模型 - 栏目 - 控制器
 *
 * @package   NiPHPCMS
 * @category  admin\logic\category
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Model.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/09/13
 */
namespace app\admin\logic\category;

use app\common\logic\Models as LogicModels;

class Model extends LogicModels
{

    /**
     * 查询栏目数据
     * @access public
     * @param
     * @return array
     */
    public function getListData()
    {
        $models = model('Models');
        $result =
        $models->field(true)
        ->paginate();

        foreach ($result as $key => $value) {
            $result[$key]->model_status = $value->model_status;
            $result[$key]->model_name = $value->model_name;
            $result[$key]->url = $value->operation_url;
        }

        return [
            'data' => $result->toArray(),
            'page' => $result->render(),
        ];
    }

    /**
     * 新增模型
     * @access public
     * @param  array  $_form_data
     * @return mixed
     */
    public function create($_form_data)
    {
        $result = parent::create($_form_data);

        if (!!$result) {
            $this->createModel($_form_data['model_table'], $_form_data['table_name'], $_form_data['remark']);
        }

        return !!$result;
    }

    /**
     * 创建新模型表
     * @access private
     * @param  string  $_model_table 基于模型表名
     * @param  string  $_table_name  新模型表名
     * @param  string  $_remark      备注
     * @return array
     */
    private function createModel($_model_table, $_table_name, $_remark)
    {
        $prefix = config('database.prefix');

        $models = model('Models');

        // 获得表结构
        $result = $models->query('SHOW CREATE TABLE `' . $prefix . $_model_table . '`');
        $create_table = $result[0]['Create Table'] . ';';

        $result = $models->query('SHOW CREATE TABLE `' . $prefix . $_model_table . '_data`');
        $create_table .= $result[0]['Create Table'];

        // 替换表名
        $create_table = str_ireplace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $create_table);
        $create_table = str_ireplace($_model_table, $_table_name, $create_table);

        // 非必要数据替换为空
        $pattern = [
            '/AUTO_INCREMENT=(\d+)/' => '',
            '/COMMENT=\'(.*?)\'/' => 'COMMENT=\'' . $_remark . '\'',
        ];
        $create_table = preg_replace(array_keys($pattern), array_values($pattern), $create_table);
        $sql = explode(';', $create_table);

        foreach ($sql as $key => $value) {
            $models->execute($value);
        }
    }

    /**
     * 获得编辑数据
     * @access public
     * @param  array  $_request_data
     * @return array
     */
    public function getEditorData($_request_data)
    {
        $map = [
            ['id', '=', $_request_data['id']],
        ];

        $models = model('Models');
        $result =
        $models->where($map)
        ->find();

        return $result ? $result->toArray() : [];
    }

    /**
     * 删除模型
     * @access public
     * @param  array  $_request_data
     * @return mixed
     */
    public function remove($_request_data)
    {
        if ($_request_data['id'] < 9) {
            $return = false;
        } else {
            $map = [
                ['id', '=', $_request_data['id']],
            ];
            $models = model('Models');

            $table_name =
            $models->where($map)
            ->value('table_name');

            $result = parent::remove($_request_data);

            if ($result) {
                $prefix = config('database.prefix');

                $drop_table = 'DROP TABLE IF EXISTS `' . $prefix . $table_name . '`;';
                $models->execute($drop_table);
                $drop_table = 'DROP TABLE IF EXISTS `' . $prefix . $table_name . '_data`;';
                $models->execute($drop_table);

                $return = true;
            } else {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * 获得基本模型数据
     * @access public
     * @param
     * @return array
     */
    public function getModels()
    {
        $map = [
            ['id', '<>', '9'],
        ];
        $model = model('Models');
        $result =
        $model->field(['id, name, table_name'])
        ->where($map)
        ->select();

        foreach ($result as $key => $value) {
            $result[$key]->model_name = $value->model_name;
        }

        return $result ? $result->toArray() : [];
    }
}
