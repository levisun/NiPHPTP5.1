<?php
/**
 *
 * 内容 - 控制器
 *
 * @package   NiPHPCMS
 * @category  application\admin\controller
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/6
 */
namespace app\admin\controller;

class Content extends Base
{

    /**
     * 内容
     * @access public
     * @param
     * @return mixed
     */
    public function content($operate = '')
    {
        if (in_array($operate, ['', 'category', 'child'])) {
            $tpl = 'content_category';
        } else {
            $tpl = 'content_' . $operate;
        }

        if (input('param.model', false)) {
            $tpl .= '_' . input('param.model');
        }

        return $this->fetch($tpl);
    }

    /**
     * 幻灯片
     * @access public
     * @param
     * @return mixed
     */
    public function banner($operate = '')
    {
        $tpl = $operate ? 'banner_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 广告
     * @access public
     * @param
     * @return mixed
     */
    public function ads($operate = '')
    {
        $tpl = $operate ? 'ads_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 评论
     * @access public
     * @param
     * @return mixed
     */
    public function comment($operate = '')
    {
        $tpl = $operate ? 'comment_' . $operate : '';
        return $this->fetch($tpl);
    }

    /**
     * 更新缓存与静态
     * @access public
     * @param
     * @return mixed
     */
    public function cache()
    {
        return $this->fetch();
    }
}
