<?php
/**
 *
 * 服务层
 * 访问日志
 *
 * @package   NiPHP
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;
use app\model\Article as ModelArticle;
use app\model\Category as ModelCategory;

class Sitemap
{

    public function FunctionName($value='')
    {
        # code...
    }

    public static function category()
    {
        $category =
        ModelCategory::view('category c', ['id', 'name', 'aliases', 'image', 'is_channel', 'access_id'])
        ->view('model m', ['name' => 'action_name'], 'm.id=c.model_id')
        ->view('level level', ['name' => 'level_name'], 'level.id=c.access_id', 'LEFT')
        ->where([
            ['c.is_show', '=', 1],
            ['c.model_id', 'in', [1,2,3]]
        ])
        ->order('c.sort_order ASC, c.id DESC')
        ->select()
        ->toArray();

        $category_xml = [];
        foreach ($category as $value) {
            $article =
            ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
            ->view('article_content article_content', ['thumb'], 'article_content.article_id=article.id', 'LEFT')
            ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
            ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id')
            ->view('level level', ['name' => 'level_name'], 'level.id=article.access_id', 'LEFT')
            ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=article.type_id', 'LEFT')
            ->where([
                ['article.category_id', '=', $value['id']],
                ['article.is_pass', '=', '1'],
                ['article.show_time', '<=', time()],
            ])
            ->order('article.is_top DESC, article.is_hot DESC , article.is_com DESC, article.sort_order DESC, article.id DESC')
            ->select()
            ->toArray();
            $article_xml = [];
            foreach ($article as $val) {
                $article_xml[]['url'] = [
                    'loc' => url('details/' . $val['action_name'] . '/' . $val['category_id'] . '/' . $val['id']),
                    'lastmod' => date('Y-m-d H:i:s', $val['update_time']),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
            if ($article_xml) {
                self::create($article_xml, 'sitemaps/list-' . $value['action_name'] . '-' . $value['id'] . '.xml');

                $category_xml[]['sitemap'] = [
                    'loc' => Request::domain() . 'sitemaps/list-' . $value['action_name'] . '-' . $value['id'] . '.xml',
                    'lastmod' => date('Y-m-d H:i:s')
                ];
            }
        }
        self::create($category_xml, 'sitemap.xml');
    }

    /**
     * 创建XML文件
     * @access private
     * @static
     * @param  array  $_data
     * @return string
     */
    public static function create(array $_data, string $_path)
    {
        $xml =  '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL .
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $xml .= self::toXml($_data) . PHP_EOL;
        $xml .= '</urlset>';
        file_put_contents(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $_path, $xml);
    }

    /**
     * 数组转XML
     * @access private
     * @static
     * @param  array  $_data
     * @return string
     */
    private static function toXml(array $_data) {
        $xml = '';
        foreach ($_data as $key => $value) {
            if (is_string($key)) {
                $xml .= '<' . $key . '>';
            }

            if (is_array($value)) {
                $xml .= PHP_EOL . self::toXml($value);
            } else {
                $xml .= $value;
            }

            if (is_string($key)) {
                $xml .= '</' . $key . '>' . PHP_EOL;
            }
        }

        return trim($xml, PHP_EOL);
    }
}
