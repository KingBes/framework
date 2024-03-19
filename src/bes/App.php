<?php

namespace bes;

use Exception;

/**
 * App 基础类
 * @property Config     $config
 */
class App
{
    // 版本
    const VERSION = '0.0.1';

    protected $config;

    /**
     * 框架目录
     * @var string
     */
    protected string $besPath = '';

    /**
     * 应用根目录
     * @var string
     */
    protected string $rootPath = '';

    /**
     * 应用目录
     * @var string
     */
    protected string $appPath = '';

    /**
     * 配置后缀
     * @var string
     */
    protected string $configExt = '.php';

    /**
     * 当前controller应用
     * 
     */
    protected $app;

    /**
     * 运行 function
     *
     * @return void
     */
    public function run()
    {
        //初始化
        $this->initialize();

        $data = $this->getAppData($this->app);
        var_dump($data);
        $view = $data["className"];
        if (isset($data["class"]->view)) {
            $view = $data["class"]->view;
        }
        $html = $this->getHtml($view);
        // 实例化
        $webview = new WebView(
            $this->config->get("app.title"),
            $this->config->get("app.width"),
            $this->config->get("app.height"),
            0,
            $this->config->get("app.debug")
        );
        foreach ($data["methods"] as $method) {
            // 创建闭包
            $newClass = $data["class"];
            $closure = function ($seq, $req, $context) use ($newClass, $method) {
                return $newClass->{$method}($seq, $req, $context);
            };
            // 调用方法并传递参数
            $webview->bind($method, $closure);
        }
        // 设置HTML
        $webview->setHTML($html);
        $webview->bind("jump", function ($seq, $req, $context) use ($webview) {
            $this->jump($webview, $req[0]);
        });
        // 运行
        $webview->run();
        // 销毁
        $webview->destroy();
        exit;
    }

    protected function jump(WebView $webview, string $req): WebView
    {
        $app = "app\controller\\$req";
        $data = $this->getAppData($app);
        $view = $data["className"];
        if (isset($data["class"]->view)) {
            $view = $data["class"]->view;
        }

        $thisData = $this->getAppData($this->app);
        foreach ($thisData["methods"] as $k => $v) {
            $webview->unbind($v);
        }

        foreach ($data["methods"] as $method) {
            // 创建闭包
            $newClass = $data["class"];
            $closure = function ($seq, $req, $context) use ($newClass, $method) {
                return $newClass->{$method}($seq, $req, $context);
            };
            // 调用方法并传递参数
            $webview->bind($method, $closure);
        }
        $html = $this->getHtml($view);
        $webview->setHTML($html);
        $this->app = $app;
        return $webview;
    }

    /**
     * 获取html function
     *
     * @param string $html
     * @return string
     */
    protected function getHtml(string $html): string
    {
        $file = $this->appPath . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . $html . ".html";
        $str = file_get_contents($file);
        return $str;
    }

    /**
     * 获取内容 function
     *
     * @param string $class
     * @return array
     */
    protected function getAppData(string $class): array
    {
        $data = [];
        if (class_exists($class)) {
            $class = new $class;
            $data["class"] = $class;
            $ReflectionClass = new \ReflectionClass($class);
            $data["className"] = $ReflectionClass->getShortName();
            foreach ($ReflectionClass->getMethods() as $k => $v) {
                $data["methods"][] = $v->name;
            }
        } else {
            return new Exception("没有$class 类");
        }

        return $data;
    }

    /**
     * 架构方法 function
     *
     * @param string $rootPath
     */
    public function __construct(string $rootPath = '')
    {
        $this->besPath   = realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $this->rootPath    = $rootPath ? rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $this->getDefaultRootPath();
        $this->appPath     = $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
        $this->config = new Config();
        $controller = glob($this->appPath . DIRECTORY_SEPARATOR . "controller" . DIRECTORY_SEPARATOR . "*.php");
        foreach ($controller as $file) {
            include_once $file;
        }
    }

    /**
     * 初始化应用
     * 
     * @return $this
     */
    public function initialize(): App
    {
        // 加载全局初始化文件
        $this->load();

        date_default_timezone_set($this->config->get('app.default_timezone', 'Asia/Shanghai'));

        return $this;
    }

    /**
     * 加载应用文件和配置
     * 
     * @return void
     */
    protected function load(): void
    {
        $appPath = $this->getAppPath();

        if (is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
        }

        include_once $this->besPath . 'helper.php';

        $configPath = $this->getConfigPath();

        $files = [];

        if (is_dir($configPath)) {
            $files = glob($configPath . '*' . $this->configExt);
        }
        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }

        $this->app = $this->config->get("app.default_app");
    }

    /**
     * 获取当前应用目录
     * 
     * @return string
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * 获取应用配置目录
     * 
     * @return string
     */
    public function getConfigPath(): string
    {

        return $this->rootPath . 'config' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取配置后缀
     * 
     * @return string
     */
    public function getConfigExt(): string
    {
        return $this->configExt;
    }

    /**
     * 获取应用根目录
     * 
     * @return string
     */
    protected function getDefaultRootPath(): string
    {
        return dirname($this->appPath, 4) . DIRECTORY_SEPARATOR;
    }

    /* public function __construct()
    {
        $this->windows = require_once BASE_PATH . '/config/windows.php';
        $this->app = require_once BASE_PATH . '/config/app.php';
        if ($this->WEBVIEW === null) {
            // 初始化
            $this->WEBVIEW = new WebView(
                title: $this->windows['title'],
                width: $this->windows['width'],
                height: $this->windows['height'],
                hint: WindowSizeHint::from($this->windows['hint']),
                debug: $this->app['debug']
            );
        }
    }

    private function jump(string $controller)
    {
        $file = APP_PATH . "view/" . $controller;
        if (!file_exists($file . ".html")) {
            echo new \Exception("view not found: " . $file);
        }
        $view = file_get_contents($file . ".html");
        $php = APP_PATH . "controller/" . $controller;
        if (!file_exists($php . ".php")) {
            echo new \Exception("controller not found: " . $php . ".php");
        }

        if (count($this->cache_methods) > 0) {
            foreach ($this->cache_methods as $k => $v) {
                $this->WEBVIEW->unbind($v);
            }
            $this->cache_methods = [];
        }
        $class = "app\\controller\\" . $controller;  // 注意使用双反斜杠转义命名空间分隔符
        $newClass = new $class;
        // 获取类的所有方法
        $methods = (new \ReflectionClass($newClass))->getMethods();
        foreach ($methods as $method) {
            $this->cache_methods[] = $method->name;
            // 创建闭包
            $closure = function ($seq, $req, $context) use ($newClass, $method) {
                return $method->invokeArgs($newClass, [$seq, $req, $context]);
            };
            // 调用方法并传递参数
            $this->WEBVIEW->bind($method->name, $closure);
        }
        $this->WEBVIEW->setHTML($view);
    }

    public function run()
    {
        $this->jump($this->app['default_controller']);
        $this->WEBVIEW
            ->bind("jump", function ($seq, $req, $context) {
                $this->jump($req[0]);
            })
            ->run()
            ->destroy();
    } */
}
