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
/*
function cache($name, $value = '')
{
    $cache = new CacheFile;
    $cache->open = false;
    $cache->prefix = '';
    $cache->expire = 300;
    $cache->cachePath = '';

    if ($name === null) {
        // 清空缓存
        $cache->clear();
    } elseif ($value === null) {
        // 删除缓存
        $cache->delete($name);
    } elseif ($value) {
        // 设置缓存
        $cache->set($name, $value);
    } else {
        $cache->get($name);
    }
}
*/

class CacheFile
{
    public $prefix    = '';         // 缓存前缀
    public $expire    = 300;        // 数据缓存有效期 0表示永久缓存
    public $subdir    = false;      // 缓存子目录(自动根据缓存标识的哈希创建子目录)
    public $check     = true;       // 是否校验缓存
    public $compress  = false;      // 是否压缩缓存
    public $cachePath = '';         // 缓存目录
    public $open      = false;      // 开关


    public function has($name)
    {
        if (!$this->open) {
            return false;
        }

        $filename = $this->filename($name);
        return is_file($filename);
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        if (!$this->open) {
            return false;
        }

        $this->delete();

        $filename = $this->filename($name);
        if (!$this->has($filename)) {
            return false;
        }
        $data = file_get_contents($filename);
        if (false !== $data) {
            $expire = (int) substr($data, 9, 12);
            if ($expire != 0 && time() > filemtime($filename) + $expire) {
                // 缓存过期删除缓存文件
                unlink($filename);
                return false;
            }

            // 开启数据校验
            if ($this->check) {
                $check = substr($data, 21, 32);
                $data = substr($data, 53, -3);
                if ($check != md5($data)) {
                    // 校验错误
                    return false;
                }
            } else {
                $data = substr($data, 21, -3);
            }

            // 启用数据压缩
            if ($this->compress && function_exists('gzcompress')) {
                $data = gzuncompress($data);
            }
            $data = htmlspecialchars_decode($data);
            $data = unserialize($data);
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param  string $name   缓存变量名
     * @param  mixed  $value  存储数据
     * @return boolen
     */
    public function set($name, $value, $expire = 0)
    {
        if (!$this->open) {
            return false;
        }

        $filename = $this->filename($name);
        $data = serialize($value);
        $data = htmlspecialchars($data);

        // 启用数据压缩
        if ($this->compress && function_exists('gzcompress')) {
            $data = gzcompress($data, 3);
        }

        // 开启数据校验
        if ($this->check) {
            $check = md5($data);
        } else {
            $check = '';
        }

        $expire = $expire ? $expire : $this->expire;
        $data = "<?php\n//>" . sprintf('%012d', $expire) . $check . $data . "\n?>";
        $result = file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolen
     */
    public function delete($name = false)
    {
        if (rand(1, 10) == 10) {
            $files = (array) glob($this->cachePath . '*');
            if (!empty($files)) {
                $rand = array_rand($files, 10);
                $days = strtotime('-7 days');
                foreach ($files as $key => $value) {
                    if (in_array($key, $rand) && $value['time'] <= $days) {
                        unlink($this->cachePath . $value['name']);
                    }
                }
            }
        }

        if ($name === false) {
            return false;
        }

        $filename = $this->filename($name);
        if (!$this->has($filename)) {
            return false;
        } else {
            return unlink($filename);
        }
    }

    /**
     * 清除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolen
     */
    public function clear()
    {
        $files = (array) glob($this->cachePath . '*');

        foreach ($files as $path) {
            if (is_dir($path)) {
                array_map('unlink', glob($path . DIRECTORY_SEPARATOR . '*.php'));
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

    /**
     * 取得变量的存储文件名
     * @access private
     * @param  string $name 缓存变量名
     * @return string
     */
    private function filename($name)
    {
        $name = md5($name);

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777);
            chmod($this->cachePath, 0777);
        }

        if ($this->subdir) {
            // 使用子目录
            $dir = substr($name, 0, 2) . DIRECTORY_SEPARATOR;

            if (!is_dir($this->cachePath . $dir)) {
                mkdir($this->cachePath . $dir, 0777);
                chmod($this->cachePath . $dir, 0777);
            }

            $filename = $dir . $this->prefix . $name . '.php';
        } else {
            $filename = $this->prefix . $name . '.php';
        }

        return $this->cachePath . $filename;
    }
}