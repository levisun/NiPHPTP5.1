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

use think\App;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use think\facade\Request;
use app\library\Base64;

class Backup
{
    private $savePath;

    public function handle($event, App $app): void
    {
        if (Request::isGet() && !in_array(Request::subDomain(), ['admin', 'api', 'cdn'])) {
            Log::record('backup', 'alert');

            $this->savePath = app()->getRuntimePath() . DIRECTORY_SEPARATOR .
                                'backup' . Base64::flag() . DIRECTORY_SEPARATOR .
                                'sys_auto' . DIRECTORY_SEPARATOR;
            if (!is_dir($this->savePath)) {
                chmod(app()->getRuntimePath(), 0777);
                mkdir($this->savePath, 0777, true);
            }

            $result = $this->queryTableName();
            foreach ($result as $key => $name) {
                if (is_file($this->savePath . $name) && filemtime($path) >= strtotime('-1 days')) {
                    $this->queryTableStructure($name);
                    $this->queryTableInsert($name);
                    break;
                } else {
                    $this->queryTableStructure($name);
                    $this->queryTableInsert($name);
                }
            }
        }
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
    private function queryTableInsert(string $_table_name, int $_num = 1000): void
    {
        set_time_limit(0);

        $result = Db::query('SHOW COLUMNS FROM `' . $_table_name . '`');
        $field = '';
        foreach ($result as $key => $value) {
            $field .= '`' . $value['Field'] . '`,';
        }
        $field = trim($field, ',');

        $total = Db::table($_table_name)->count();
        $total = ceil($total / $_num);

        $size = 0;
        $num = 1;
        for ($i = 0; $i < $total; $i++) {
            $first_row = $i * $_num;
            $data = Db::table($_table_name)
            ->field($field)
            ->limit($first_row, $_num)
            ->select();

            foreach ($data as $value) {
                $sql_file = $this->savePath . $_table_name . '_' . $num . '.sql';
                if (!is_file($sql_file)) {
                    $insert = 'INSERT INTO `' . $_table_name . '` (' . $field . ') VALUES' . PHP_EOL;
                    file_put_contents($sql_file, $insert);
                } elseif (is_file($sql_file) && filesize($sql_file) >= 1024 * 1024 * 3) {
                    $insert = file_get_contents($sql_file);
                    $insert = trim($insert, ',' . PHP_EOL) . ';' . PHP_EOL;
                    file_put_contents($sql_file, $insert);

                    $num++;
                    $sql_file = $this->savePath . $_table_name . '_' . $num . '.sql';
                    $insert = 'INSERT INTO `' . $_table_name . '` (' . $field . ') VALUES' . PHP_EOL;
                    file_put_contents($sql_file, $insert, FILE_APPEND);
                }
                clearstatcache();

                $insert = '';
                foreach ($value as $vo) {
                    if (is_integer($vo)) {
                        $insert .= $vo . ',';
                    } elseif (is_null($vo) || $vo == 'null' || $vo == 'NULL') {
                        $insert .= 'NULL,';
                    } else {
                        $insert .= '\'' . addslashes($vo) . '\',';
                    }
                }
                $insert = '(' . trim($insert, ',') . '),' . PHP_EOL;

                file_put_contents($sql_file, $insert, FILE_APPEND);
            }
        }

        if (!empty($sql_file) && is_file($sql_file)) {
            $insert = file_get_contents($sql_file);
            $insert = trim($insert, ',' . PHP_EOL) . ';' . PHP_EOL;
            file_put_contents($sql_file, $insert);
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
        set_time_limit(0);

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
        set_time_limit(0);

        $result = Db::query('SHOW TABLES FROM ' . Config::get('database.database'));
        $tables = array();
        foreach ($result as $key => $value) {
            $value = current($value);
            $tables[str_replace(Config::get('database.prefix'), '', $value)] = $value;
        }
        return $tables;
    }
}
