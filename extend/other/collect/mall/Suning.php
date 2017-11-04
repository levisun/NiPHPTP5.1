<?php
/**
 *
 */
class Suning extends Base
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
        $url = 'https://search.suning.com/emall/mobile/wap/clientSearch.jsonp?cityId=010&channel=&ps=10&st=0&set=5&cf=&iv=-1&ci=&ct=-1&channelId=WAP&sp=&sg=&sc=&prune=&operate=0&isAnalysised=0&istongma=1&v=99999999&callback=success_jsonpCallback';

        $page--;
        $url .= '&cp=' . $page . '&keyword=' . $search;

        $result = $this->snoopy($url);
        $result = substr($result, 22);
        $result = substr($result, 0, -2);

        $json = json_decode($result, true);

        $item = array();
        foreach ($json['goods'] as $key => $value) {
            $item[] = array(
                'image' => 'https://image3.suning.cn/uimg/b2c/newcatentries/' . $value['salesCode'] . '-000000000' . $value['catentryId'] . '_1_400x400.jpg',
                'name'  => $value['catentdesc'],
                'url'   => urlencode('//m.suning.com/product/' . $value['salesCode'] . '/' . $value['catentryId'] . '.html'),
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
        $url = 'https:' . urldecode($url);
        $result = $this->snoopy($url);

        $detail['url'] = $url;

        preg_match('/("productName": ")(.*?)(",)/', $result, $matches);
        $detail['title'] = $matches[2];

        preg_match_all('/(<img ori-src=")(.*?)(")/', $result, $matches);
        foreach ($matches[2] as $key => $value) {
            $detail['images'][] = 'https:' . $value . '400x400.jpg';
        }
        preg_match_all('/(image">[\s]<img data-src=")(.*?)(")/', $result, $matches);
        foreach ($matches[2] as $key => $value) {
            $detail['images'][] = 'https:' . $value . '400x400.jpg';
        }

        // 请求所需参数
        preg_match('/("passPartNumber": ")(.*?)(",)/', $result, $matches);
        $passPartNumber = $matches[2];

        preg_match('/("supplierCode": ")(.*?)(",)/', $result, $matches);
        $supplierCode = $matches[2];

        preg_match('/("categoryCode_mdm": ")(.*?)(",)/', $result, $matches);
        $categoryCode_mdm = $matches[2];

        preg_match('/("brandCode": ")(.*?)(",)/', $result, $matches);
        $brandCode = $matches[2];

        preg_match('/(<div class="desc-spec-param desc-spec-item">)(.*?)(<div class="tab-desc desc-sale">)/', $result, $matches);
        preg_match_all('/(<div>)(.*?)(<\/div>)/', $matches[2], $matches);
        $prop = array();
        foreach ($matches[2] as $key => $value) {
            $k = $key * 2;
            $kp = $k + 1;
            if (isset($matches[2][$k])) {
                $prop[] = array(
                    $matches[2][$k] => $matches[2][$kp]
                );
            }
        }
        $detail['prop'] = array(
            '规格参数' => $prop
        );

        $url = 'https://pas.suning.com/nssnitemsale_' . $passPartNumber . '_' . $supplierCode . '_250_029_0290199_0_5__999_1_____1000257.html?callback=wapData';
        $price = file_get_contents($url);
        $price = substr($price, 8);
        $price = substr($price, 0, -2);
        $json = json_decode($price, true);
        $detail['price'] = isset($json['data']['price']['saleInfo'][0]['promotionPrice']) ?
        $json['data']['price']['saleInfo'][0]['promotionPrice'] :
        $json['data']['price']['saleInfo'][0]['netPrice'];

        $url = 'http://product.m.suning.com/pds-web/ajax/selfUniqueInfoJsonp_' . $passPartNumber . '_' . $supplierCode . '_' . $categoryCode_mdm . '_' . $brandCode . '_itemUnique.html?callback=itemUnique';
        $desc = $this->snoopy($url);
        $desc = substr($desc, 11);
        $desc = substr($desc, 0, -1);
        $json = json_decode($desc, true);
        $detail['desc'] = $json['itemDetail']['phoneDetail'];
        $detail['desc'] = str_replace('src2', 'src', $detail['desc']);

        return $detail;
    }
}
