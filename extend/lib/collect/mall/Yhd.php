<?php
/**
 *
 */
class Yhd extends Base
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
        $url = 'http://search.m.yhd.com/search/k';
        $url .= $search;
        $url .= '/p' .$page . '-s1-si1-t1?req.ajaxFlag=1';
        $result = $this->snoopy($url);

        preg_match_all('/(<a href=")(.*?)(" class="item">)/si', $result, $matches);
        $url = $matches[2];

        preg_match_all('/(<div class="pic_box">)(.*?)(<\/div>)/si', $result, $matches);
        preg_match_all('/(src="|original=")(.*?)(")/si', implode(' ', $matches[2]), $matches);
        $img = array();
        foreach ($matches[2] as $key => $value) {
            if (strpos($value, '.svg') === false) {
                $img[] = $value;
            }
        }

        preg_match_all('/(<div class="title_box">)(.*?)(<\/div>)/si', $result, $matches);
        $title = $matches[2];

        preg_match_all('/(<small>¥<\/small><i>)(.*?)(<\/i>)/si', $result, $matches);
        $price = $matches[2];

        $item = array();
        foreach ($url as $key => $value) {
            $title[$key] = str_replace('<span class="self_sell">自营</span>', '', $title[$key]);
            $item[] = array(
                'image' => 'http:' . $img[$key],
                'name'  => trim($title[$key]),
                'url'   => urlencode($value),
                'price' => $price[$key],
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

        preg_match('/(<h2 class="pd_product-title" id="pd_product-title">)(.*?)(<\/h2>)/si', $result, $matches);
        $detail['title'] = $matches[2];

        preg_match('/(<span class="pd_product-price-num">)(.*?)(<\/span>)/si', $result, $matches);
        $detail['price'] = $matches[2];

        preg_match_all('/(data-src=")(.*?)(")/si', $result, $matches);
        $detail['images'] = $matches[2];

        preg_match('/(detailparams={)(.*?)(};)/si', $result, $matches);
        preg_match('/(h5proSignature:")(.*?)(",)/si', $matches[2], $m);
        $h5proSignature = $m[2];
        preg_match('/(productId:)(.*?)(,)/si', $matches[2], $m);
        $productId = $m[2];
        preg_match('/(pmId:)(.*?)(,)/si', $matches[2], $m);
        $pmId = $m[2];

        $url = 'http://item.m.yhd.com/item/ajaxProductDesc.do?callback=jsonp12&productId=' . $productId . '&pmId=' . $pmId . '&uid=' . $h5proSignature;
        $result = $this->snoopy($url);
        $json = substr($result, 8);
        $json = substr($json, 0, -1);
        $json = json_decode($json, true);
        $detail['desc'] = $json['data'][0]['tabDetail'];

        $prop = array();
        foreach ($json['data'][1]['productParamsVoList'][0]['descParamVoList'] as $key => $value) {
            $prop[] = array(
                $value['attributeName'] => $value['attributeValue']
            );
        }
        $detail['prop'] = array(
            '规格参数' => $prop
        );

        return $detail;
    }
}
