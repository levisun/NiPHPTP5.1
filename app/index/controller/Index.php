<?php
namespace app\index\controller;

use think\App;
use think\ViewController;
use think\facade\Config;
use think\facade\Env;
use think\facade\Lang;
use app\common\library\Filter;
use app\common\library\Html;
use app\common\library\Siteinfo;

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

        $theme_name = Siteinfo::theme();

        // 初始化模板
        $config = Config::get('template.');
        $config['view_path'] = Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR .
                               'theme' . DIRECTORY_SEPARATOR .
                               $app->getName() . DIRECTORY_SEPARATOR .
                               $theme_name . DIRECTORY_SEPARATOR;
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
    protected function fetch(string $_template = '')
    {
        $_template = $_template ? Config::get('template.view_path') . $_template : $_template;
        return parent::fetch($_template . '.' . Config::get('template.view_suffix'));
    }

    public function index(string $action = 'index')
    {
        return $this->fetch($action);
    }
}
