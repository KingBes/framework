<?php

declare(strict_types=1);

namespace app;

use app\PhpWebview\WebView;
use Exception;

class KingBes
{
    /**
     * webview variable
     *
     * @var WebView
     */
    protected static WebView $wv;

    /**
     * 绑定信息 variable
     *
     * @var array
     */
    protected array $binds = [];

    /**
     * 构造函数 function
     *
     * @param object $config 配置
     */
    public function __construct(
        protected object $config = new Config()
    ) {
        $this->load_config();
        date_default_timezone_set($this->config->get("app.default_timezone", "Asia/Shanghai"));
        self::$wv = new WebView(
            $this->config->get("app.windows.title", "PHP GUI"),
            $this->config->get("app.windows.width", 640),
            $this->config->get("app.windows.height", 480),
            $this->config->get("app.windows.debug", true),
        );
    }

    /**
     * 运行 function
     *
     * @return void
     */
    public function run(): void
    {
        self::$wv->icon_title($this->config->get("app.windows.title", "PHP GUI"));
        $configMenu = $this->config->get("menu");
        if (!empty($configMenu)) {
            self::$wv->icon_menu($configMenu);
        }
        self::$wv->bind(
            "app_jump",
            function ($seq, $req, $context) {
                $page = isset($req[0]) ? $req[0] : "";
                $this->controller($page);
                $this->view($page);
            }
        );
        $this->controller();
        $this->view();
        self::$wv->run();
        self::$wv->destroy();
        exit; //结束
    }

    /**
     * 控制器 function
     *
     * @param string $name
     * @return void
     */
    protected function controller(string $name = ""): void
    {
        // 默认Home控制器
        if ($name == "") {
            $name = $this->config->get("app.default_controller", "Home");
        }
        // 卸载旧的绑定
        foreach ($this->binds as $b) {
            self::$wv->unbind($b);
        }
        // 重新配置控制器内容
        $class = "app\\controller\\{$name}";
        if (class_exists($class)) {
            $instantiated_class = new $class;
            $Ref = new \ReflectionClass($instantiated_class);
            $methods = [];
            foreach ($Ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->name !== "app_jump") {
                    $methods[] = $method->name;
                    $Closure = function ($seq, $req, $context)
                    use ($instantiated_class, $method) {
                        return $instantiated_class->{$method->name}($seq, $req);
                    };
                    self::$wv->bind($method->name, $Closure);
                }
            }
            // 重新赋值绑定信息
            $this->binds = $methods;
        } else {
            throw new Exception("No {$class} class found");
        }
    }

    /**
     * 视图 function
     *
     * @param string $name
     * @return void
     */
    protected function view(string $name = ""): void
    {
        if ($name == "") {
            $name = $this->config->get("app.default_controller", "Home");
        }
        $path = base_path(
            $this->config->get("app.default_view.dirname"),
            "{$name}.{$this->config->get("app.default_view.suffix")}"
        );
        if (is_file($path)) {
            $html = file_get_contents($path);
            self::$wv->setHTML($html);
        } else {
            throw new Exception("View '{$path}' file does not exist");
        }
    }

    /**
     * 获取app function
     *
     * @return WebView
     */
    public static function get_app(): WebView
    {
        return self::$wv;
    }

    /**
     * 配置文件夹 variable
     *
     * @var string
     */
    protected string $configDir = "config";

    /**
     * 配置 variable
     *
     * @var string
     */
    protected string $configExt = ".php";

    /**
     * 获取配置文件目录 function
     *
     * @return string
     */
    public function getConfigDir(): string
    {
        return base_path($this->configDir);
    }

    /**
     * 获取配置后缀 function
     *
     * @return string
     */
    public function getConfigExt(): string
    {
        return $this->configExt;
    }

    /**
     * 加载配置文件 function
     *
     * @return void
     */
    protected function load_config(): void
    {
        $pathDir = $this->getConfigDir();
        $files = [];
        if (is_dir($pathDir)) {
            $files = glob("{$pathDir}*{$this->configExt}");
        }
        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
    }
}
