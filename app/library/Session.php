<?php
/**
 *
 * 服务层
 * 数据安全过滤
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
namespace app\library;

use SessionHandlerInterface;
use think\facade\Config;
use app\model\Session as ModelSession;

class Session implements SessionHandlerInterface
{
    private $prefix;
    private $expire;

    /**
     * 构造方法
     * @access public
     * @param
     * @return void
     */
    public function __construct($_config = [])
    {
        $this->config = !empty($_config) ? $_config : Config::get('session');

        $this->prefix = Config::get('session.prefix');
        $this->expire = Config::get('session.expire');
    }

    /**
     * 打开Session
     * @access public
     * @param  string    $_savePath
     * @param  mixed     $_sessName
     * @return boolean
     */
    public function open($_savePath, $_sessName): bool
    {
        return true;
    }

    /**
     * 关闭Session
     * @access public
     * @param
     * @return boolean
     */
    public function close(): bool
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param  string $_sessID
     * @return mixed
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
        ModelSession::where($map)
        ->value('data');

        return $result ? $result : '';
    }

    /**
     * 写入Session
     * @access public
     * @param  string    $_sessID
     * @param  string    $_sessData
     * @return bool
     */
    public function write($_sessID, $_sessData): bool
    {
        $result =
        ModelSession::where([
            ['session_id', '=', $this->prefix . $_sessID]
        ])
        ->find()
        ->toArray();

        $data = [
            'session_id'  => $this->prefix . $_sessID,
            'data'        => $_sessData ? $_sessData : '',
            'update_time' => time()
        ];

        if (!empty($result)) {
            ModelSession::where([
                ['session_id', '=', $this->prefix . $_sessID],
            ])
            ->update($data);
            return !!ModelSession::getNumRows();
        } else {
            ModelSession::insert($data);
            return !!ModelSession::getNumRows();
        }
    }

    /**
     * 删除Session
     * @access public
     * @param  string $_sessID
     * @return bool
     */
    public function destroy($_sessID): bool
    {
        ModelSession::where([
            ['session_id', '=', $this->prefix . $_sessID]
        ])
        ->delete();
        return !!ModelSession::getNumRows();

    }

    /**
     * Session 垃圾回收
     * @access public
     * @param  string $sessMaxLifeTime
     * @return true
     */
    public function gc($_sessMaxLifeTime): bool
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

        ModelSession::where($map)
        ->delete();
        return !!ModelSession::getNumRows();
    }
}
