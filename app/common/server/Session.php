<?php
/**
 *
 * 数据安全过滤 - 业务层
 *
 * @package   NiPHP
 * @category  app\common\server
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\common\server;

use SessionHandlerInterface;
use think\facade\Config;
use app\common\model\Session as SessionModel;

class Session implements SessionHandlerInterface
{
    private $prefix;
    private $expire;

    /**
     * 构造方法
     * @access public
     * @param  Request $_request Request对象
     * @return void
     */
    public function __construct($_config = '')
    {
        $this->config = $_config ? $_config : Config::get('session.');

        $this->prefix = Config::get('session.prefix');
        $this->expire = Config::get('session.expire');
    }

    /**
     * 打开Session
     * @access public
     * @param  string    $_savePath
     * @param  mixed     $_sessName
     */
    public function open($_savePath, $_sessName)
    {
        $this->handler = new SessionModel;

        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param  string $_sessID
     */
    public function read($_sessID)
    {
        $map = [
            ['session_id', '=', $this->prefix . $_sessID]
        ];

        if ($this->expire != 0) {
            $map[] = ['update_time', '>=', time() - $this->expire];
        }
        $result =
        $this->handler
        ->where($map)
        ->value('data');

        $result = json_decode($result, true);
        return serialize($result);
    }

    /**
     * 写入Session
     * @access public
     * @param  string    $_sessID
     * @param  string    $_sessData
     * @return bool
     */
    public function write($_sessID, $_sessData)
    {
        $result =
        $this->handler
        ->where([
            ['session_id', '=', $this->prefix . $_sessID]
        ])
        ->find();

        $data = [
            'session_id'  => $this->prefix . $_sessID,
            'data'        => $_sessData ? json_encode($_sessData) : '',
            'update_time' => time()
        ];

        if ($result) {
            $res = $this->handler->editor($data);
        } else {
            $res = $this->handler->added($data);
        }

        return $res ? true : false;
    }

    /**
     * 删除Session
     * @access public
     * @param  string $_sessID
     * @return bool
     */
    public function destroy($_sessID)
    {
        return
        $this->handler
        ->remove([
            'session_id' => $this->prefix . $_sessID,
        ]);
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param  string $sessMaxLifeTime
     * @return true
     */
    public function gc($_sessMaxLifeTime)
    {
        if ($this->expire != 0) {
            $map = [
                ['update_time', '<=', time() - $this->expire]
            ];
        } else {
            $_sessMaxLifeTime = $_sessMaxLifeTime ? $_sessMaxLifeTime : 86400;
            $map = [
                ['update_time', '<=', time() - $_sessMaxLifeTime]
            ];
        }

        $result =
        $this->handler
        ->where($map)
        ->delete();

        return $result ? true : false;
    }
}
