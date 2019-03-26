<?php
/**
 *
 * 服务层
 * 备份类
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\facade\Config;
use think\facade\Db;
use app\library\Base64;

class Backup
{
    private $savePath;

    /**
     * 构造方法
     * @access public
     * @param
     * @return void
     */
    public function __construct()
    {
    }

    public function run(string $_tag = '')
    {
        $this->savePath = app()->getRuntimePath() . DIRECTORY_SEPARATOR .
                            'backup' . Base64::flag() . DIRECTORY_SEPARATOR .
                            date('ymdH') . $_tag . DIRECTORY_SEPARATOR;

        if (!is_dir($this->savePath)) {
            chmod(app()->getRuntimePath(), 0777);
            mkdir($this->savePath, 0777, true);
        } else {
            return false;
        }

        $result = $this->queryTableName();
        foreach ($result as $key => $name) {
            $this->queryTableStructure($name);
            $this->queryTableInsert($name);
        }
    }

    /**
     * 表数据
     * @access private
     * @param
     * @return void
     */
    private function queryTableInsert(string $_table_name): void
    {
        $result = Db::query('SHOW COLUMNS FROM `' . $_table_name . '`');
        $field = '';
        foreach ($result as $key => $value) {
            $field .= '`' . $value['Field'] . '`,';
        }
        $field = trim($field, ',');

        $total = Db::table($_table_name)->count();
        $total = ceil($total / 500);
        for ($i = 0; $i < $total; $i++) {
            $insert = 'INSERT INTO `' . $_table_name . '` (' . $field . ') VALUES' . PHP_EOL;
            $first_row = $i * 500;
            $data = Db::table($_table_name)
            ->field($field)
            ->limit($first_row, 500)
            ->select();

            foreach ($data as $value) {
                $insert .= '(';
                foreach ($value as $vo) {
                    if (is_integer($vo)) {
                        $insert .= $vo . ',';
                    } elseif (is_null($vo) || $vo == 'null' || $vo == 'NULL') {
                        $insert .= 'NULL,';
                    } else {
                        $insert .= '\'' . addslashes($vo) . '\',';
                    }
                }
                $insert = trim($insert, ',');
                $insert .= '),' . PHP_EOL;
            }
            $insert = trim($insert, ',' . PHP_EOL) . ';' . PHP_EOL;
            $num = 1000000 + $i;
            file_put_contents($this->savePath . $_table_name . $num . '.sql', $insert);
        }
    }

    /**
     * 表结构
     * @access private
     * @param
     * @return void
     */
    private function queryTableStructure(string $_table_name): void
    {
        $tableRes = Db::query('SHOW CREATE TABLE `' . $_table_name . '`');
        $structure = '';
        if (!empty($tableRes[0]['Create Table'])) {
            $structure = 'DROP TABLE IF EXISTS `' . $_table_name . '`;' . PHP_EOL;
            $structure .= $tableRes[0]['Create Table'] . ';';
            file_put_contents($this->savePath . $_table_name . '.sql', $structure);
        }
    }

    /**
     * 数据库表名
     * @access private
     * @param
     * @return array
     */
    private function queryTableName(): array
    {
        $result = Db::query('SHOW TABLES FROM ' . Config::get('database.database'));
        $tables = array();
        foreach ($result as $key => $value) {
            $value = current($value);
            $tables[str_replace(Config::get('database.prefix'), '', $value)] = $value;
        }
        return $tables;
    }
}
