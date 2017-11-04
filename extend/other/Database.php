<?php
/**
 * 缓存类
 *
 * @package   NiPHPCMS
 * @category  extend\util\
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version   CVS: $Id: Db.php v1.0.1 $
 * @link      www.NiPHP.com
 * @since     2017/08/18
 */

/**
*
*/
class Database
{
    private $links;

    /**
     * 连接数据库
     * @access public
     * @param  array  $config 连接参数
     * @return void
     */
    public function connect($config = array())
    {
        try {
            if (isset($config['dsn'])) {
                $dsn = $config['dsn'];
            } else {
                $dsn = $this->parseDsn($config);
            }

            $params = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => false
            );

            $this->links = new PDO($dsn, $config['username'], $config['password'], $params);
            $this->links->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'PDO Exception Caught:<br>';
            echo 'Error with the database:<br>';
            echo '<pre>';
            echo 'ERROR:' . $e->getMessage() . '<br>';
            echo 'Code:' . $e->getCode() . '<br>';
            echo 'File:' . $e->getFile() . '<br>';
            echo 'Line:' . $e->getLine() . '<br>';
            echo 'Trace:' . $e->getTraceAsString() . '<br>';
            echo '</pre>';
        }
    }

    /**
     * DSN解析
     * @access private
     * @param  array   $config
     * @return string
     */
    private function parseDsn($config)
    {
        $dsn = $config['db_type'] . ':';
        $dsn .= 'host=' . $config['db_host'] . ';';
        $dsn .= 'port=' . $config['db_port'] . ';';
        $dsn .= 'dbname=' . $config['db_name'];
        return $dsn;
    }

    /**
     * 执行语句
     * @access protected
     * @param  string $sql sql指令
     * @param  array  $bind 参数绑定
     * @return object
     */
    protected function execute($sql, $bind = array())
    {
        try {
            $PDOStatement = $this->links->prepare($sql);
            $PDOStatement->execute($bind);
            return $PDOStatement;
        } catch (PDOException $e) {
            echo 'PDO Exception Caught:<br>';
            echo 'Error with the database:<br>';
            echo 'SQL Query:' . $sql;
            echo '<pre>';
            echo 'ERROR:' . $e->getMessage() . '<br>';
            echo 'Code:' . $e->getCode() . '<br>';
            echo 'File:' . $e->getFile() . '<br>';
            echo 'Line:' . $e->getLine() . '<br>';
            echo 'Trace:' . $e->getTraceAsString() . '<br>';
            echo '</pre>';
        }
    }

    /**
     * 新增数据
     * @access public
     * @param
     * @return int
     */
    public function add($sql)
    {
        $row = $this->execute($sql)->rowCount();
        $this->last_insert_id = $this->links->lastInsertId();

        return $this->last_insert_id;
    }

    /**
     * 执行SQL
     * @access public
     * @param
     * @return boolean
     */
    public function query($sql)
    {
        return $this->execute($sql);
    }

    /**
     * 删除|修改数据
     * @access public
     * @param
     * @return int
     */
    public function deleteOrUpdate($sql)
    {
        return $this->execute($sql)->rowCount();
    }

    /**
     * 查询数据
     * @access public
     * @param
     * @return array
     */
    public function select($sql)
    {
        return $this->execute($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 表数据总数
     * @access public
     * @param
     * @return int
     */
    public function total($sql)
    {
        return $this->execute($sql)->fetchObject();
    }
}
