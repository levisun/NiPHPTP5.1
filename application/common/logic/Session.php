<?php
/**
 *
 * 数据安全过滤 - 业务层
 *
 * @package   NiPHP
 * @category  application\admin\logic\account
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */
namespace app\common\logic;

use SessionHandlerInterface;
use app\common\model\Session as SessionModel;

class Session implements SessionHandlerInterface
{
    private $handler;
    private $config;

    public function __construct($_config = '')
    {
        $this->config = $_config ? $_config : config('session.');
    }

    /**
     * 打开Session
     * @access public
     * @param  string    $_savePath
     * @param  mixed     $_sessName
     */
    public function open($_savePath, $_sessName)
    {
        // $this->handler = model('common/session');
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
            ['session_id', '=', $this->config['prefix'] . $_sessID]
        ];

        if ($this->config['expire'] != 0) {
            $map[] = ['update_time', '>=', time() - $this->config['expire']];
            // $map['update_time'] = ['gt', time() - $this->config['expire']];
        }
        $result =
        $this->handler
        ->where($map)
        ->value('data');

        return json_decode($result, true);
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
            ['session_id', '=', $this->config['prefix'] . $_sessID]
        ])
        ->find();

        $data = [
            'session_id'  => $this->config['prefix'] . $_sessID,
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
            'session_id' => $this->config['prefix'] . $_sessID,
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
        if ($this->config['expire'] != 0) {
            $map = [
                ['update_time', '<=', time() - $this->config['expire']]
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
