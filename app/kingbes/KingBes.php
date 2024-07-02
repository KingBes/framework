<?php

declare(strict_types=1);

namespace app\kingbes;

use app\kingbes\PhpWebview\WebView;
use Exception;
use app\kingbes\PhpWebview\Dialog;
use think\facade\Db;

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
    protected static array $binds = [];

    /**
     * app模块ID variable
     *
     * @var string
     */
    protected static string $appModId = "PhpDebug";

    protected static object $config;
    protected static object $dialog;

    /**
     * 构造函数 function
     *
     * @param object $config 配置
     */
    public function __construct()
    {
        self::$config = new Config();
        self::$dialog = new Dialog();
        $this->set_mode_id();
        $this->load_config();
        date_default_timezone_set(self::$config->get(
            "app.default_timezone",
            "Asia/Shanghai"
        ));
        self::$wv = new WebView(
            self::$config->get("app.windows.title", "PHP GUI"),
            self::$config->get("app.windows.width", 640),
            self::$config->get("app.windows.height", 480),
            self::$config->get("app.windows.debug", true),
        );
        // 启动数据库
        Db::setConfig(self::$config->get("database"));
    }

    /**
     * 运行 function
     *
     * @return void
     */
    public function run(): void
    {
        $this->init();
        self::$wv->icon_title(self::$config->get("app.windows.title", "PHP GUI"));
        $configMenu = self::$config->get("menu");
        if (!empty($configMenu)) {
            self::$wv->icon_menu($configMenu);
        }
        self::$wv->bind(
            "app_jump",
            function ($seq, $req, $context) {
                $page = isset($req[0]) ? $req[0] : "";
                self::app_jump($page);
            }
        );
        self::app_jump("");
        self::$wv->run();
        self::$wv->destroy();
        exit; //结束
    }

    /**
     * 视图初始js配置 function
     *
     * @return void
     */
    protected function init(): void
    {
        $js = "";
        if (!self::$config->get("app.windows.debug", true)) {
            $js .= <<<EOF
document.addEventListener('DOMContentLoaded', function() {  
    document.addEventListener('contextmenu', function(e) {  
        console.log("非开发者模式不得右键~")
        console.log("Non-developer mode cannot right-click~")
        if (!e.target.matches('img')) {  
            e.preventDefault(); // 如果不是 <img>，则阻止默认右键菜单  
        }  
    }); 
});  
EOF;
        }
        self::$wv->init($js);
    }

    /**
     * 控制器 function
     *
     * @param string $name
     * @return void
     */
    protected static function controller(string $name): void
    {
        // 重新配置控制器内容
        $class = "app\\controller\\{$name}";
        if (class_exists($class)) {
            $instantiated_class = new $class;
            $Ref = new \ReflectionClass($instantiated_class);

            if (
                isset($instantiated_class->width) &&
                isset($instantiated_class->height) &&
                isset($instantiated_class->hint)
            ) {
                self::$wv->size(
                    $instantiated_class->width,
                    $instantiated_class->height,
                    $instantiated_class->hint
                );
            }

            if (isset($instantiated_class->title)) {
                self::$wv->setTitle($instantiated_class->title);
            }

            foreach ($Ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->name !== "app_jump") {
                    self::$binds[] = $method->name;
                    $Closure = function ($seq, $req, $context)
                    use ($instantiated_class, $method) {
                        return $instantiated_class->{$method->name}((int)$seq, $req);
                    };
                    self::$wv->bind($method->name, $Closure);
                }
            }
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
    protected static function view(string $name): void
    {
        $suffix = self::$config->get("app.default_view.suffix");
        $path = base_path(
            self::$config->get("app.default_view.dirname"),
            "{$name}.{$suffix}"
        );
        if (is_file($path)) {
            self::$wv->navigate($path);
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
     * 添加和js交互函数绑定 function
     *
     * @param string $name 绑定函数名
     * @param \Closure $function $seq, $req 函数
     * @return void
     */
    public static function bind_method(string $name, \Closure $function): void
    {
        foreach (self::$binds as $k => $v) {
            if ($v === $name) {
                throw new Exception("Double-bound to the existing controller");
                break;
            } else {
                continue;
            }
        }
        self::$binds[] = $name;
        $Closure = function ($seq, $req, $context)
        use ($function) {
            return $function($seq, $req);
        };
        self::$wv->bind($name, $Closure);
    }

    /**
     * 对话框 function
     *
     * @return object
     */
    public static function dialog(): object
    {
        return self::$dialog;
    }

    /**
     * 获取模块id function
     *
     * @return string
     */
    public static function get_mode_id(): string
    {
        return self::$appModId;
    }

    /**
     * 跳转 function
     *
     * @return void
     */
    public static function app_jump(string $page = ""): void
    {
        // 卸载旧的绑定
        foreach (self::$binds as $b) {
            self::$wv->unbind($b);
        }
        self::$binds = [];
        
        if (trim($page)  == "") {
            $page = self::$config->get("app.default_controller", "Home");
        }
        $res = true;
        $middlewares = self::$config->get("middleware", []);
        foreach ($middlewares as $middleware) {
            $class = new $middleware;
            $class_next = "app\\controller\\{$page}";
            if (class_exists($class_next)) {
                $Closure = $class->process($page, new $class_next);
                if (!$Closure) {
                    $res = $Closure;
                    break;
                }
            } else {
                throw new Exception("No {$class_next} class found");
            }
        }
        if ($res) {
            self::controller($page);
            self::view($page);
        }
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
        return app_path($this->configDir);
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
            self::$config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
    }

    /**
     * 设置模块id function
     *
     * @return void
     */
    protected function set_mode_id(): void
    {
        if (isset(debug_backtrace()[0]["file"])) {
            $path = debug_backtrace()[0]["file"];
            // $work_dir = dirname($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            if ($type === "exe") {
                self::$appModId = $path;
            }
        } elseif (isset(debug_backtrace()[1]["file"])) {
            $path = debug_backtrace()[1]["file"];
            // $work_dir = dirname($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            if ($type === "exe") {
                self::$appModId = $path;
            }
        } elseif (isset(debug_backtrace()[2]["file"])) {
            $path = debug_backtrace()[2]["file"];
            // $work_dir = dirname($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            if ($type === "exe") {
                self::$appModId = $path;
            }
        }
    }
}
