<?php
/**
 *
 * 服务层
 * 上传类
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

// use think\App;
// use think\Response;
// use think\exception\HttpException;
// use think\exception\HttpResponseException;
// use think\facade\Cache;
// use think\facade\Config;
use think\facade\Env;
// use think\facade\Lang;
// use think\facade\Log;
use think\facade\Request;
// use app\library\Accesslog;
// use app\library\Base64;
// use app\library\Filter;
// use app\library\Siteinfo;

class Upload
{
    private $validate = [
        'img' => [
            'size' => 100*1024,
            'ext'  => ['jpg', 'gif', 'png']
        ],
        'zip' => [
            'size' => 100*1024,
            'ext'  => ['zip', 'rar']
        ],
        'office' => [
            'size' => 100*1024,
            'ext'  => ['doc', 'ppt']
        ]
    ];

    private $savePath;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct()
    {
        $this->savePath = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                          'uploads' . DIRECTORY_SEPARATOR .
                          date('Ym') . DIRECTORY_SEPARATOR;

        $this->tempPath = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                          'temp' . DIRECTORY_SEPARATOR;
    }

    public function img()
    {
        $file = Request::file('upload');
        $file->validate($this->validate['img'])->rule('uniqid');

        if ($result = $file->move($this->tempPath)) {
            # code...
        }
    }

    public function images()
    {
        $file = Request::file('upload');
        $file->validate($this->validate['img'])->rule('uniqid');

        if ($result = $file->move($this->tempPath)) {
            # code...
        }
    }

    public function zip()
    {
        $file = Request::file('upload');
        $file->validate($this->validate['zip'])->rule('uniqid');

        if ($result = $file->move($this->tempPath)) {
            # code...
        }
    }

    public function office()
    {
        $file = Request::file('upload');
        $file->validate($this->validate['office'])->rule('uniqid');

        if ($result = $file->move($this->tempPath)) {
            # code...
        }
    }
}
