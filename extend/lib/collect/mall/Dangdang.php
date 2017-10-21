<?php
/**
 *
 */
class Dangdang extends Base
{

    /**
     * 商品列表
     * @access public
     * @param  string $search
     * @param  int    $page
     * @return array
     */
    public function page($search, $page = 1)
    {
        $url = 'http://search.m.dangdang.com/search_ajax.php?act=get_product_flow_search';
        $url .= '&t=' . time() . '&page=' . $page . '&keyword=' . $search;
        $result = $this->snoopy($url);
        $json = json_decode($result, true);

        $item = array();
        foreach ($json['products'] as $key => $value) {
            $item[] = array(
                'image' => $value['image_url'],
                'name'  => $value['name'],
                'url'   => urlencode(substr($value['product_url'], 5)),
                'price' => $value['price'],
                );
        }

        return $item;
    }

    /**
     * 商品详情
     * @access public
     * @param  string $url
     * @return array
     */
    public function detail($url)
    {
        $url = 'http:' . urldecode($url);
        $result = $this->snoopy($url);

        $detail['url'] = $url;

        preg_match('/(<article>)(.*?)(<\/article>)/si', $result, $matches);
        $detail['title'] = $matches[2];

        preg_match('/(<span id="main_price">)(.*?)(<\/span>)/si', $result, $matches);
        $detail['price'] = $matches[2];

        preg_match('/(<ul class="top-slider" style="width:500%;">)(.*?)(<\/ul>)/si', $result, $matches);
        $images = $matches[2];
        preg_match('/(<img src=")(.*?)(")/si', $images, $m);
        $detail['images'][] = $m[2];
        preg_match_all('/(imgsrc=")(.*?)(")/si', $images, $m);
        foreach ($m[2] as $key => $value) {
            $detail['images'][] = $value;
        }

        preg_match('/(<a dd_name="顶部详情" href=")(.*?)(")/si', $result, $matches);
        $desc = $this->snoopy($matches[2]);
        preg_match('/(<section data-content-name="详情" class="area j_area">)(.*?)(<\/section>)/si', $desc, $matches);
        $detail['desc'] = $matches[2];

        preg_match('/(<section data-content-name="规格参数" class="area j_area">)(.*?)(<\/section>)/si', $desc, $matches);

        preg_match_all('/(<em>)(.*?)(<\/em>)/si', $matches[2], $m);
        preg_match_all('/(<i>)(.*?)(<\/i>)/si', $matches[2], $i);

        $prop = array();
        foreach ($m[2] as $key => $value) {
            $prop[] = array(
                $value => $i[2][$key]
            );
        }
        $detail['prop'] = array(
            '规格参数' => $prop
        );

        return $detail;
    }
}
