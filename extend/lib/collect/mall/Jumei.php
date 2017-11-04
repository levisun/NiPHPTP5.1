<?php
/**
 *
 */
class Jumei extends Base
{

    /**
     * 商品列表
     * @access public
     * @param  array $search
     * @param  int   $page
     * @return array
     */
    public function page($search, $page = 1)
    {
        $url = 'http://h5.jumei.com/search/index?search=';
        $url .= $search;
        $url .= '&page=' . $page . '&ajax=get';

        $result = $this->snoopy($url);
        $json = json_decode($result, true);
        $item = array();
        foreach ($json['data']['item_list'] as $key => $value) {
            $item[] = array(
                'image' => $value['image_url_set']['single']['320'],
                'name'  => $value['name'],
                'url'   => urlencode('//h5.jumei.com/product/detail?type=' . $value['type'] . '&item_id=' . $value['item_id']),
                'price' => $value['jumei_price'],
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
        $arr = explode('?', $url);
        $id = $arr[1];

        $q_url = 'http://h5.jumei.com/product/ajaxStaticDetail?' . $id;
        $result = $this->snoopy($q_url);
        $json = json_decode($result, true);

        $detail = array(
            'url' => $url,
            'title' => $json['data']['name'],
        );

        foreach ($json['data']['image_url_set']['single_many'] as $key => $value) {
            $detail['images'][] = $value['480'];
        }

        $detail['desc'] = $json['data']['description_info']['description'];

        $prop = array();
        foreach ($json['data']['properties'] as $key => $value) {
            $prop[] = array(
                $value['name'] => $value['value'],
            );
        }
        $detail['prop'] = array(
            '规格参数' => $prop
        );

        $q_url = 'http://h5.jumei.com/product/ajaxDynamicDetail?' . $id;
        $result = $this->snoopy($q_url);
        $json = json_decode($result, true);
        $detail['price'] = $json['data']['result']['size'][0]['jumei_price'];

        return $detail;
    }
}
