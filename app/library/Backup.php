<?php
/**
 *
 * 服务层
 * 备份类
 *
 * @package   NICMS
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
            Log::record('[BACKUP] 备份', 'alert');
            $this->savePath = app()->getRuntimePath() . DIRECTORY_SEPARATOR .
                                'backup' . Base64::flag() . DIRECTORY_SEPARATOR .
                                'sys_auto' . DIRECTORY_SEPARATOR;

            if (!is_dir($this->savePath)) {
                chmod(app()->getRuntimePath(), 0777);
                mkdir($this->savePath, 0777, true);
            }

            if (!is_file($this->savePath . 'backup.lock')) {
                ignore_user_abort(true);
                file_put_contents($this->savePath . 'backup.lock', 'lock');
                $result = $this->queryTableName();
                foreach ($result as $key => $name) {
                    if (rand(1, 2) === 1) {
                        continue;
                    }
                    file_put_contents($this->savePath . 'backup.lock', $name);
                    if (is_file($this->savePath . $name . '.sql') && filemtime($this->savePath . $name . '.sql') < strtotime('-3 days')) {
                        Log::record('backup:' . $name, 'alert');
                        $this->queryTableStructure($name);
                        $this->queryTableInsert($name);
                        break;
                    } elseif (!is_file($this->savePath . $name . '.sql')) {
                        Log::record('backup:' . $name, 'alert');
                        $this->queryTableStructure($name);
                        $this->queryTableInsert($name);
                    }
                }
            }
            unlink($this->savePath . 'backup.lock');
            ignore_user_abort(false);
            clearstatcache();
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

            $result = $this->queryTableName();
            foreach ($result as $key => $name) {
                $this->queryTableStructure($name);
                $this->queryTableInsert($name);
            }
        }

        clearstatcache();
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

        $num = 1;
        $sql_file = $this->savePath . $_table_name . '_' . sprintf('%07d', $num) . '.sql';
        $insert_into  = '/* ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;
        $insert_into .= 'INSERT INTO `' . $_table_name . '` (' . $field . ') VALUES' . PHP_EOL;
        $insert_data = '';

        for ($i = 0; $i < $total; $i++) {
            $first_row = $i * $_num;
            $data = Db::table($_table_name)
            ->field($field)
            ->limit($first_row, $_num)
            ->select();

            foreach ($data as $key => $value) {
                foreach ($value as $vo) {
                    if (is_integer($vo)) {
                        $insert_data .= $vo . ',';
                    } elseif (is_null($vo) || $vo == 'null' || $vo == 'NULL') {
                        $insert_data .= 'NULL,';
                    } else {
                        $insert_data .= '\'' . addslashes($vo) . '\',';
                    }
                }
                $insert_data = trim($insert_data, ',') . '),' . PHP_EOL . '(';

                if ($key % 10 == 0 && strlen($insert_data) >= 1024 * 1024 * 3) {
                    if ($insert_data) {
                        $insert_data = '(' . trim($insert_data, ',' . PHP_EOL . '(') . ';' . PHP_EOL;
                        file_put_contents($sql_file, $insert_into . $insert_data);
                        $num++;
                        $sql_file = $this->savePath . $_table_name . '_' . sprintf('%07d', $num) . '.sql';
                        $insert_data = '';
                    }
                }
            }
        }

        if ($insert_data) {
            $insert_data = '(' . trim($insert_data, ',' . PHP_EOL . '(') . ';' . PHP_EOL;
            file_put_contents($sql_file, $insert_into . $insert_data);
        }
        unset($sql_file, $insert_data, $num, $data, $result);
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
        $structure = '/* ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;
        if (!empty($tableRes[0]['Create Table'])) {
            $structure .= 'DROP TABLE IF EXISTS `' . $_table_name . '`;' . PHP_EOL;
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
