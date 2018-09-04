<?php
/**
 *
 * 数据库备份 - 扩展 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\admin\logic\expand
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/6
 */
namespace app\admin\logic\expand;

use think\Model;

class Databack extends Model
{
    // protected $connection = 'db_config1';

    /**
     * 查询
     * @access public
     * @param
     * @return array
     */
    public function query($value = '')
    {
        $dir  = env('root_path') . 'backup' . DIRECTORY_SEPARATOR . '*';
        $file = (array) glob($dir);
        rsort($file);

        $file_dir = [];
        foreach ($file as $key => $value) {
            $name = basename($value);

            if ($name == 'index.html') {
                continue;
            }

            $name = substr($name, 0, -4);

            $file_dir[] = [
                'id'   => encrypt($name),
                'name' => $name,
                'time' => date('Y-m-d H:i:s', filectime($value)),
                'size' => file_size($value),

            ];
        }

        return $file_dir;
    }

    /**
     * 还原数据库
     * @access public
     * @param
     * @return void
     */
    public function reduction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $receive_data = [
            'type'      => input('post.type/f'),
            'id'        => input('post.id'),
            '__token__' => input('post.__token__'),
        ];

        // 安全还原
        // 还原数据库前备份当前库
        if ($receive_data['type'] === 1) {
            $this->backup();
        }

        $TEMP_DIR = $this->createDir();

        $zip = new \Pclzip('');
        $zip->zipname = env('root_path') . 'backup' . DIRECTORY_SEPARATOR . decrypt($receive_data['id']) . '.zip';
        $zip->extract(PCLZIP_OPT_PATH, $TEMP_DIR);

        $file = (array) glob($TEMP_DIR . '*');
        sort($file);

        foreach ($file as $key => $path) {
            if (is_file($path)) {
                $sql = file_get_contents($path);
                $sql = json_decode($sql);
                $bol = parent::batchQuery($sql);
                unlink($path);
            }
        }
        rmdir($TEMP_DIR);

        return true;
    }

    /**
     * 备份数据库
     * @access public
     * @param  integer $_limit
     * @return void
     */
    public function backup($_limit = 1000)
    {
        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $TEMP_DIR = $this->createDir();

        $receive_data = [
            'backup_type' => input('post.backup_type/f'),
            'table_name'  => input('post.table_name/a'),
            '__token__'   => input('post.__token__'),
        ];

        if ($receive_data['backup_type'] == 1) {
            $receive_data['table_name'] = $this->getTablesName();
        }

        $TABLES_SQL = '';
        foreach ($receive_data['table_name'] as $table_name) {
            $tableRes = parent::query('SHOW CREATE TABLE `' . $table_name . '`');
            if (empty($tableRes[0]['Create Table'])) {
                continue;
            }
            $TABLES_SQL = [
                'DROP TABLE IF EXISTS `' . $table_name . '`;',
                $tableRes[0]['Create Table'] . ';',
            ];

            $table_field = parent::query('SHOW COLUMNS FROM `' . $table_name . '`');

            $INSERT_SQL = 'INSERT INTO `' . $table_name . '` (';
            foreach ($table_field as $field) {
                $INSERT_SQL .= '`' . $field['Field'] . '`,';
            }
            $INSERT_SQL = trim($INSERT_SQL, ',');
            $INSERT_SQL .= ') VALUES ';

            $count =
            $this->table($table_name)
            ->count();

            $count = ceil($count / $_limit);

            for ($i=0; $i < $count; $i++) {
                $first_row = $i * $_limit;

                $table_data =
                $this->table($table_name)
                ->limit($first_row, $_limit)
                ->select();

                $VALUES = '';
                foreach ($table_data as $field_value) {
                    $field_value = $field_value->toArray();

                    $VALUES .= '(';
                    foreach ($field_value as $val) {
                        if (is_integer($val)) {
                            $VALUES .= $val . ',';
                        } elseif (is_null($val) || $val == 'null' || $val == 'NULL') {
                            $VALUES .= 'NULL,';
                        } else {
                            $VALUES .= '\'' . addslashes($val) . '\',';
                        }
                    }
                    $VALUES = trim($VALUES, ',');
                    $VALUES .= '),';
                }
                $VALUES = trim($VALUES, ',');
                $VALUES .= ';';

                $num = 1000000 + $i;
                if ($i == 0) {
                    $TABLES_SQL[] = $INSERT_SQL . $VALUES;
                    file_put_contents(
                        $TEMP_DIR . $table_name . '_' . $num .  '.sql',
                        json_encode($TABLES_SQL, JSON_UNESCAPED_UNICODE)
                    );
                    unset($TABLES_SQL);
                } else {
                    file_put_contents(
                        $TEMP_DIR . $table_name . '_' . $num .  '.sql',
                        json_encode([$INSERT_SQL . $VALUES], JSON_UNESCAPED_UNICODE)
                    );
                }
            }
        }

        $this->createZip($TEMP_DIR);
        $this->removeDir($TEMP_DIR);

        return true;
    }

    /**
     * 压缩文件
     * @access private
     * @param  string $_dir
     * @return void
     */
    private function createZip($_dir)
    {
        $zip = new \Pclzip('');
        $zip->zipname = env('root_path') . 'backup' . DIRECTORY_SEPARATOR . date('YmdHis') . '.zip';
        $zip->create($_dir, PCLZIP_OPT_REMOVE_PATH, $_dir);
    }

    /**
     * 删除临时目录
     * @access private
     * @param  string $_dir
     * @return void
     */
    private function removeDir($_dir)
    {
        if (is_dir($_dir)) {
            if (substr($_dir, -1) === DIRECTORY_SEPARATOR) {
                $file = glob($_dir . '*');
            } else {
                $file = glob($_dir . DIRECTORY_SEPARATOR . '*');
            }

            foreach ($file as $file_name) {
                if (is_file($file_name)) {
                    unlink($file_name);
                }
            }

            rmdir($_dir);
        }
    }

    /**
     * 创建临时目录
     * @access private
     * @param
     * @return string
     */
    private function createDir()
    {
        $temp_dir = env('runtime_path') . DIRECTORY_SEPARATOR . 'backup_temp' . session(config('user_auth_key')) . DIRECTORY_SEPARATOR;
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755);
            chmod($temp_dir, 0755);
        }

        return $temp_dir;
    }

    /**
     * 获得库中所有表名
     * @access public
     * @param
     * @return array
     */
    public function getTablesName()
    {
        $result = parent::query('SHOW TABLES FROM ' . config('database.database'));

        $tables = array();
        foreach ($result as $key => $value) {
            $tables[] = current($value);
        }
        return $tables;
    }
}
