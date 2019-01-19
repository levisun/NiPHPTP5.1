<?php
/**
 *
 * 缓解并发 - 中间件
 *
 * @package   NiPHP
 * @category  application\common\middleware
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/9
 */

namespace app\common\middleware;

class Concurrent
{
    private $request;

    /**
     * 并发压力
     * @access public
     * @param
     * @return void
     */
    public function handle($_request, \Closure $_next)
    {
        $this->request = $_request;

        // 搜索引擎不执行
        if ($this->isSpider() === false) {
            // 千分之一抛出异常
            if (!APP_DEBUG && rand(1, 1000) === 1) {
                abort(502);
            }

            // 拦截频繁请求
            // 拦截非法请求
            if ($this->intercept()) {
                trace('[INTERCEPT]', 'alert');
                abort(502);
            }
        }

        return $_next($_request);
    }

    /**
     * 拦截频繁请求
     * 拦截非法请求
     * @access private
     * @param
     * @return void
     */
    private function intercept()
    {
        $key = md5('CIP' . $this->request->ip());
        if (!cache('?' . $key)) {
            $result = [
                'expire'  => 60,
                'runtime' => time(),
                'total'   => 0,
            ];
        } else {
            $result = cache($key);
        }

        // 非法请求
        if ($result['total'] >= 50) {
            return true;
        }
        // 更新请求数
        elseif ($result['runtime'] + $result['expire'] >= time()) {
            $result['total']++;
        }
        // 还原请求
        else {
            $result = [
                'expire'  => 60,
                'runtime' => time(),
                'total'   => 0,
            ];
        }

        cache($key, $result, 0);

        return false;
    }

    /**
     * 判断搜索引擎蜘蛛
     * @access protected
     * @param
     * @return mixed
     */
    protected function isSpider()
    {
        $searchengine = [
            'GOOGLE'         => 'googlebot',
            'GOOGLE ADSENSE' => 'mediapartners-google',
            'BAIDU'          => 'baiduspider',
            'MSN'            => 'msnbot',
            'YODAO'          => 'yodaobot',
            'YAHOO'          => 'yahoo! slurp;',
            'Yahoo China'    => 'yahoo! slurp china;',
            'IASK'           => 'iaskspider',
            'SOGOU'          => 'sogou web spider',
            'SOGOU'          => 'sogou push spider',
            'YISOU'          => 'yisouspider',
        ];

        $user_agent = strtolower($this->request->server('HTTP_USER_AGENT'));
        foreach ($searchengine as $key => $value) {
            if (preg_match('/(' . $value . ')/si', $user_agent)) {
                return $key;
            }
        }
        return false;
    }
}
