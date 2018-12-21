<?php
/**
 *
 * API - 控制器
 *
 * @package   NiPHP
 * @category  application\api\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2017/12
 */
namespace app\api\controller;

use app\common\logic\Async;

class Api extends Async
{
    protected $handleMethod = [
        'login',
        'logout',
        'added',
        'editor',
        'remove',
        'sort',
    ];

    protected $uploadMethod = [
        'upload',
    ];

    public function handle()
    {
        $this->methodAuth('handle')->token();
    }

    public function upload()
    {
        $this->methodAuth('upload')->token();
    }

    /**
     * 获得IP地址地区信息
     * @access public
     * @param
     * @return json
     */
    public function getipinfo()
    {
        $result = logic('common/IpInfo')->getInfo(input('param.ip'));
        $this->success('QUERY SUCCESS', $result);
    }

    /**
     * 错误页面
     * @access public
     * @param
     * @return
     */
    public function abort()
    {
        abort(404);
    }

    /**
     * 验证METHOD AUTH
     * @access protected
     * @param
     * @return mixed
     */
    protected function methodAuth($_type)
    {
        if ($_type === 'handle' && !in_array($this->action, $this->handleMethod)) {
            $this->error('[METHOD] ' . $this->method . ' error');
        } elseif ($_type === 'upload' && !in_array($this->action, $this->uploadMethod)) {
            $this->error('[METHOD] ' . $this->method . ' error');
        } elseif ($_type === 'query') {
            if (in_array($this->action, $this->handleMethod) || in_array($this->action, $this->uploadMethod)) {
                $this->error('[METHOD] ' . $this->method . ' error');
            }
        }

        return $this;
    }
}
