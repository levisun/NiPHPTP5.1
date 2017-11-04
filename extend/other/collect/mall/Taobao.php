<?php
/**
 *
 */
class Taobao extends Base
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
        $url = '//s.m.taobao.com/search?event_submit_do_new_search_auction=1&_input_charset=utf-8&topSearch=1&atype=b&searchfrom=1&action=home%3Aredirect_app_action&from=1&sst=1&n=20&buying=buyitnow&m=api4h5&abtest=18&wlsort=18&page=' . $page . '&q=' . $search;

        $result = $this->snoopy('https:' . $url);
        $result = json_decode($result, true);
        $item = array();
        foreach ($result['listItem'] as $key => $value) {
            if ($value['isP4p'] == 'false') {
                $item[$key] = array(
                    'mall_type' => 'taobao',
                    'image'     => $value['img2'],
                    'name'      => $value['title'],
                    'price'     => $value['priceWap'],
                );

                if (strpos($value['url'], '.tmall.com')) {
                    $item[$key]['url'] = urlencode($value['url']);
                } else {
                    $value['url'] = '//h5.m.taobao.com/awp/core/detail.htm?id=' . $value['item_id'];
                    $value['url'] = '//h5api.m.taobao.com/h5/mtop.taobao.detail.getdetail/6.0/?data={"exParams":"{"id":"' . $value['item_id'] . '"}","itemNumId":"' . $value['item_id'] . '"}';
                    $item[$key]['url'] = urlencode($value['url']);
                }
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
        if (strpos($url, '.tmall.com')) {
            $tmall = new Tmall;
            return $tmall->detail($url);
        } else {
            $url = str_replace('{', '%7B', $url);
            $url = str_replace('}', '%7D', $url);
            $url = str_replace('\"', '%22', $url);
            $url = str_replace(':', '%3A', $url);
            $url = str_replace(',', '%2C', $url);
            $url = 'http:' . $url;
            $result = $this->snoopy($url);
            $json = json_decode($result, true);
            $json['data']['mockData'] = json_decode($json['data']['mockData'], true);

            $detail = array(
                'id'     => $json['data']['item']['itemId'],
                'title'  => $json['data']['item']['title'],
                'images' => $json['data']['item']['images'],
                'price'  => $json['data']['mockData']['price']['price']['priceText'],
                'desc'   => 'http://api.m.taobao.com/h5/mtop.taobao.detail.getdesc/6.0/?data=%7B%22id%22%3A%22' . $json['data']['item']['itemId'] . '%22%2C%22type%22%3A%220%22%2C%22f%22%3A%22TB14Unck8cHL1JjSZJi8qwKcpla%22%7D',
                'prop'   => $json['data']['props']['groupProps'][0],
            );

            $desc = $this->snoopy($detail['desc']);
            $desc = json_decode($desc, true);
            $desc = $desc['data']['wdescContent']['pages'];
            $desc = preg_replace('/(<img size=[a-zA-Z0-9]+>)/si', '<img src="', $desc);
            $desc = str_replace('</img>', '" />', $desc);

            $desc = str_replace('<txt>', '<p>', $desc);
            $desc = str_replace('</txt>', '</p>', $desc);

            $detail['desc'] = implode('', $desc);
        }

        $detail['url'] = 'http://h5.m.taobao.com/awp/core/detail.htm?id=' . $detail['id'];

        return $detail;
    }
}
