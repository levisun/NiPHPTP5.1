<?php
/**
 *
 * 服务层
 * 访问日志
 *
 * @package   NICMS
 * @category  app\library
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2019
 */
declare (strict_types = 1);

namespace app\library;

use think\App;
use think\facade\Lang;
use think\facade\Request;
use app\model\Article as ModelArticle;
use app\model\Category as ModelCategory;

class Sitemap
{

    public function handle($event, App $app)
    {
        $path = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'sitemap.xml';
        if (is_file($path) && filemtime($path) < strtotime('-1 days')) {
            Log::record('[SITEMAP] 网站地图', 'alert');

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

            $sitemap_xml = [];
            foreach ($category as $vo_cate) {
                $article =
                ModelArticle::view('article article', ['id', 'category_id', 'title', 'keywords', 'description', 'access_id', 'update_time'])
                ->view('article_content article_content', ['thumb'], 'article_content.article_id=article.id', 'LEFT')
                ->view('category category', ['name' => 'cat_name'], 'category.id=article.category_id')
                ->view('model model', ['name' => 'action_name'], 'model.id=category.model_id')
                ->view('level level', ['name' => 'level_name'], 'level.id=article.access_id', 'LEFT')
                ->view('type type', ['id' => 'type_id', 'name' => 'type_name'], 'type.id=article.type_id', 'LEFT')
                ->where([
                    ['article.category_id', '=', $vo_cate['id']],
                    ['article.is_pass', '=', '1'],
                    ['article.show_time', '<=', time()],
                ])
                ->order('article.id DESC')
                ->limit(100)
                ->select()
                ->toArray();
                $article_xml = [];
                $category_xml = [];
                foreach ($article as $vo_art) {
                    $article_xml[]['url'] = [
                        'loc'        => url('details/' . $vo_art['action_name'] . '/' . $vo_art['category_id'] . '/' . $vo_art['id']),
                        'lastmod'    => date('Y-m-d H:i:s', $vo_art['update_time']),
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];

                    $category_xml[]['url'] = [
                        'loc'        => url('list/' . $vo_cate['action_name'] . '/' . $vo_cate['id']),
                        'lastmod'    => date('Y-m-d H:i:s', $vo_art['update_time']),
                        'changefreq' => 'daily',
                        'priority'   => '1.0',
                    ];
                }
                if ($article_xml) {
                    self::create($article_xml, 'sitemaps/details-' . $vo_cate['action_name'] . '-' . $vo_cate['id'] . '.xml');
                    self::create($category_xml, 'sitemaps/list-' . $vo_cate['action_name'] . '-' . $vo_cate['id'] . '.xml');

                    $sitemap_xml[]['sitemap'] = [
                        'loc'     => Request::domain() . 'sitemaps/details-' . $vo_cate['action_name'] . '-' . $vo_cate['id'] . '.xml',
                        'lastmod' => date('Y-m-d H:i:s')
                    ];
                    $sitemap_xml[]['sitemap'] = [
                        'loc'     => Request::domain() . 'sitemaps/list-' . $vo_cate['action_name'] . '-' . $vo_cate['id'] . '.xml',
                        'lastmod' => date('Y-m-d H:i:s')
                    ];
                }
            }
            self::create($sitemap_xml, 'sitemap.xml');
        }
    }

    /**
     * 创建XML文件
     * @access private
     * @param  array  $_data
     * @return string
     */
    private function create(array $_data, string $_path): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL .
               '<!-- generated-on="' . date('Y-m-d H:i:s') . '" -->' . PHP_EOL .
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $xml .= self::toXml($_data) . PHP_EOL;
        $xml .= '</urlset>';
        file_put_contents(app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . $_path, $xml);
    }

    /**
     * 数组转XML
     * @access private
     * @param  array  $_data
     * @return string
     */
    private function toXml(array $_data): string
    {
        $xml = '';
        foreach ($_data as $key => $value) {
            if (is_string($key)) {
                $xml .= '<' . $key . '>';
            }

            if (is_array($value)) {
                $xml .= PHP_EOL . self::toXml($value) . PHP_EOL;
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
