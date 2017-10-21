<?php
/**
 *
 */
class Tmall extends Base
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
        $url = '//s.m.taobao.com/search?event_submit_do_new_search_auction=1&_input_charset=utf-8&topSearch=1&atype=b&searchfrom=1&action=home%3Aredirect_app_action&from=1&q=' . $search . '&sst=1&n=20&buying=buyitnow&m=api4h5&abtest=26&wlsort=26&style=mid&closeModues=nav%2Cselecthot%2Conesearch&filter=tab_mall&page=' . $page;

        $result = $this->snoopy('https:' . $url);
        $result = json_decode($result, true);

        $item = array();
        foreach ($result['listItem'] as $key => $value) {
            if ($value['isP4p'] == 'false') {
                $item[$key] = array(
                    'mall_name' => '天猫商城',
                    'mall_type' => 'tmall',
                    'image'     => $value['img2'],
                    'name'      => $value['title'],
                    'url'       => urlencode($value['url']),
                    'price'     => $value['priceWap'],
                );
            }
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
        $url    = 'https:' . urldecode($url);
        $result = $this->snoopy($url, array(), 'UTF-8');

        preg_match('/(var _DATA_Detail = {)(.*?)(};)/si', $result, $matches);

        $json = json_decode('{' . $matches[2] . '}', true);

        $detail = array(
            'id'     => $json['item']['itemId'],
            'title'  => $json['item']['title'],
            'images' => $json['item']['images'],
            'price'  => $json['mock']['price']['price']['priceText'],
            'desc'   => $json['jumpUrl']['apis']['httpsDescUrl'],
            'prop'   => $json['props']['groupProps'][0],
        );

        // 采集详情
        $desc = $this->snoopy('https:' . $detail['desc'], array(), 'UTF-8');
        $desc = str_replace('var desc=', '', $desc);
        $detail['desc'] = trim($desc, "'");
        $detail['url'] = $url;

        return $detail;
    }
}
