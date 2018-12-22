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
namespace app\api\logic;

use app\common\logic\Async;

class Cms extends Async
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

    protected function initialize()
    {
        $this->module = 'cms';
    }

    public function query()
    {
        $result = $this->token()->run()->sign()->methodAuth('query')->send();
        if (!is_null($result)) {
            $this->success('QUERY SUCCESS', $result);
        } else {
            $this->error('404', 'ABORT:404', '404');
        }
    }

    public function handle()
    {
        $this->token()->run()->sign()->methodAuth('handle');
    }

    public function upload()
    {
        $this->token()->run()->sign()->methodAuth('upload');
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
