<?php

namespace bes;

class App
{
    // 版本
    const VERSION = '0.0.1';
    // webview
    private $WEBVIEW = null;
    // app配置
    public array $app = [];
    // windows配置
    public array $windows = [];
    // 缓存方法
    private array $cache_methods = [];
    
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
