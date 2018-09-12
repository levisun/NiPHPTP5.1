<?php
/**
 *
 * 导航 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Nav
{

    /**
     * 查询导航
     * @access public
     * @param  int $_type_id
     * @return array
     */
    public function query($_type_id = 1)
    {
        $result =
        model('common/category')
        ->field('id,name,pid,aliases,seo_title,seo_keywords,seo_description,image,url,is_channel,model_id')
        ->where([
            ['type_id', '=', input('param.type_id/f', (int) $_type_id)],
            ['is_show', '=', 1],
            ['pid', '=', 0],
            ['lang', '=', lang(':detect')],
        ])
        ->order('sort ASC, id DESC')
        ->cache(!APP_DEBUG)
        ->select()
        ->toArray();

        return $this->queryChild($result);
    }

    /**
     * 查询子导航
     * @access protected
     * @param  array $data
     * @return array
     */
    protected function queryChild($_data)
    {
        $nav = [];

        foreach ($_data as $key => $value) {
            $nav[$key] = $value;

            $nav[$key]['url'] = $this->getUrl($value['model_id'], $value['is_channel'], $value['id']);

            $result =
            model('common/category')
            ->field('id,name,pid,aliases,seo_title,seo_keywords,seo_description,image,url,is_channel,model_id')
            ->where([
                ['lang', '=', lang(':detect')],
                ['pid', '=', $value['id']]
            ])
            ->order('sort ASC, id DESC')
            ->cache(!APP_DEBUG)
            ->select();
            $result = $result->toArray();

            if (!empty($result)) {
                // 递归查询子类
                $_child = $this->queryChild($result);
                $result = !empty($_child) ? $_child : $result;
                $nav[$key]['child'] = $result;
            }
        }

        return $nav;
    }

    /**
     * 获得导航指向地址
     * Breadcrumb.php Sidebar.php 调用
     * @access public
     * @param  int    $_model_id   模型ID
     * @param  int    $_is_channel 是否频道页
     * @param  int    $_cat_id     导航ID
     * @return string
     */
    public function getUrl($_model_id, $_is_channel, $_cat_id)
    {
        if ($_is_channel) {
            $url = 'channel/' . $_cat_id;
        } else {
            switch ($_model_id) {
                case 1:
                case 2:
                case 3:
                case 7:
                    $url = 'list/' . $_cat_id;
                    break;

                case 4:
                    $url = 'page/' . $_cat_id;
                    break;

                case 5:
                    $url = 'feedback/' . $_cat_id;
                    break;

                case 6:
                    $url = 'message/' . $_cat_id;
                    break;

                case 8:
                    $url = 'link/' . $_cat_id;
                    break;

                default:
                    # code...
                    break;
            }
        }

        return url($url, [], 'html', true);
    }
}
