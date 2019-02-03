<?php
namespace app\index\controller;

use think\App;
use think\ViewController;
use think\facade\Config;
use think\facade\Env;
use app\common\library\Filter;
use app\common\library\Html;

class Index extends ViewController
{
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     * @return void
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $config = Config::get('template.');
        $config['view_path'] = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                               'theme' . DIRECTORY_SEPARATOR .
                               $this->request->app() . DIRECTORY_SEPARATOR .
                               Config::get($this->request->app() . '_theme', 'default') .
                               DIRECTORY_SEPARATOR;
        Config::set('template.view_path', $config['view_path']);

        $this->view->engine('Think', $config);

        $this->filter(function($_content){
            $_content = Filter::XXE($_content);
            $_content = Filter::XSS($_content);
            $_content = Filter::FUN($_content);

            $html = new Html;
            $_content = $html->meta() . trim($_content) . $html->foot();
            $html->build($_content);

            return $_content;
        });
    }

    /**
     * 加载模板输出
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetch(string $template = '')
    {
        if ($template) {
            $template = Config::get('template.view_path') . $template;
        }

        return parent::fetch($template . '.' . Config::get('template.view_suffix'));
    }

    public function index()
    {
        return $this->fetch('index');
        // return '<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script><script>$.ajax({
        //     url: "http://www.tp5.com/index/index/hello.html",
        //     type: "get",
        //     headers: {
        //         "accept": "application/vnd.tp5.v1.0.1+json",
        //         "authentication": "f0c4b4105d740747d44ac6dcd78624f906202706",
        //     },
        //     data: {
        //         method: "account.user.login"
        //     },
        // });</script>';
        // return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.2<br/><span style="font-size:30px">12载初心不改 - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello()
    {
        $a = new \app\common\server\Api;
        $a->app_name = 'admin';
        $a->run()->send();
    }
}
