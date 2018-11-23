<?php
/**
 *
 * 反馈 - 业务层
 *
 * @package   NiPHPCMS
 * @category  application\cms\logic
 * @author    失眠小枕头 [levisun.mail@gmail.com]
 * @copyright Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @link      www.NiPHP.com
 * @since     2018/8
 */
namespace app\cms\logic;

class Feedback
{

    public function added()
    {
        $ip_info = logic('common/logic/IpInfo')->getInfo();

        $receive_data = [
            'title'       => input('post.title'),
            'username'    => input('post.username'),
            'content'     => input('post.content', '', config('content_filter')),
            'category_id' => input('post.category_id/f'),
            'type_id'     => input('post.type_id/f', 0),
            'mebmer_id'   => input('post.mebmer_id/f', 0),
            'is_pass'     => input('post.is_pass/f', 0),
            'ip'          => $ip_info['ip'],
            'ip_attr'     => $ip_info['country'] . $ip_info['region'] .
                             $ip_info['city'] . $ip_info['area'],
            'lang'        => lang(':detect'),
        ];

        $result = validate('cms/feedback', input('post.'));
        if (true !== $result) {
            return $result;
        }
    }

    public function queryInput($_cid = 0)
    {
        $_cid = $_cid ? (float) $_cid : input('param.cid/f');

        $fields =
        model('common/fields')
        ->view('fields f', ['name' => 'fields_name'])
        ->view('fields_type fd', ['name' => 'fields_type', 'regex' => 'fields_regex'], 'fd.id=f.type_id')
        ->where([
            ['f.category_id', '=', $_cid],
        ])
        ->cache(!APP_DEBUG ? __METHOD__ . $_cid : false)
        ->select()
        ->toArray();

        $type =
        model('common/type')
        ->where([
            ['category_id', '=', $_cid],
        ])
        ->select()
        ->toArray();

        $fields[] = [
            'fields_name' => 'title'
        ]

        halt($fields);
        return [
            'input' => $fields,
            'type'  => $type
        ];
    }
}
