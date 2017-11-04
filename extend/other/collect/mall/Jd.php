<?php
/**
 *
 */
class Jd extends Base
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
        $url = 'https://so.m.jd.com/ware/searchList.action';
        $form = array(
            '_format_' => 'json',
            'sort'     => '',
            'keyword'  => $search,
            'page'     => $page,
            );

        $result = $this->snoopy($url, $form);
        $json = json_decode($result, true);
        $json = json_decode($json['value'], true);

        $item = array();
        foreach ($json['wareList']['wareList'] as $key => $value) {
            $item[] = array(
                'image' => $value['imageurl'],
                'name'  => $value['wname'],
                'url'   => urlencode('//item.m.jd.com/product/' . $value['wareId'] . '.html'),
                'price' => $value['jdPrice'],
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
        $id = substr($url, 24);
        $id = substr($id, 0, -5);
        $_url = 'https://item.m.jd.com/ware/detail.json?wareId=' . $id;
        $result = $this->snoopy($_url);
        $json = json_decode($result, true);
        $json['ware']['wi']['code'] = json_decode($json['ware']['wi']['code'], true);

        $detail = array(
                'id'     => $id,
                'title'  => $json['ware']['wname'],
                'desc'   => $json['wdis'],
                'url'    => 'https:' . $url,
            );

        if (isset($json['ware']['popWareDetailWebViewMap']['cssContent'])) {
            $detail['desc'] = $json['ware']['popWareDetailWebViewMap']['cssContent'] . $detail['desc'];
        }

        foreach ($json['ware']['images'] as $key => $value) {
            $detail['images'][] = $value['bigpath'];
        }

        foreach ($json['ware']['wi']['code'] as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $ke => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            $detail['prop'][$ke][] = $v;
                        }
                    } else {
                        $detail['prop'][' '][][$ke] = $val;
                    }
                }
            }
        }

        $result = $this->snoopy($detail['url']);
        preg_match('/(<span class="plus-jd-price-text" id="specJdPrice"> )(.*?)( <\/span>)/', $result, $matches);
        $detail['price'] = $matches[2];

        return $detail;
    }
}
