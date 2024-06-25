<?php

declare(strict_types=1);

namespace app\PhpWebview;

use Closure;
use FFI;
use OsException;

class WebView
{
    private FFI $ffi;

    private $webview;

    protected WindowSizeHint $hint = WindowSizeHint::HINT_NONE;

    /**
     * @param string $title 标题
     * @param int $width 宽度
     * @param int $height 高度
     * @param bool $debug debug
     * @param string|null $libraryFile 拓展文件路径
     * @param WindowSizeHint $hint
     * @throws OsException
     * @throws FFI\Exception
     */
    public function __construct(
        protected string         $title,
        protected int            $width,
        protected int            $height,
        protected bool           $debug = false,
        protected ?string        $libraryFile = null,
    ) {
        $headerContent = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'webview_php.h');
        $this->ffi = FFI::cdef($headerContent, $this->getDefaultLibraryFile());
        $this->webview = $this->ffi->webview_create((int)$this->debug, $this->width, $this->height, null);
    }

    /**
     * 获取ffi function
     *
     * @return FFI
     */
    public function getFFI(): FFI
    {
        return $this->ffi;
    }

    /**
     * 获取webview function
     *
     * @return mixed
     */
    public function getWebview(): mixed
    {
        return $this->webview;
    }

    /**
     * 获取标题 function
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getHint(): WindowSizeHint
    {
        return $this->hint;
    }

    public function setHint(WindowSizeHint $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setHTML(string $html): self
    {
        $this->ffi->webview_set_html($this->webview, $html);

        return $this;
    }

    public function returnValue($seq, $req, object|array $value): self
    {
        $this->ffi->webview_return($this->webview, $seq, $req, json_encode($value));

        return $this;
    }

    public function bind($name, Closure $function, ?Context $context = null): self
    {
        $newFunction = function ($seq, $req, $args) use ($context, $function) {
            $value = $function($seq, json_decode($req), $context);
            if ($value && (is_object($value) || is_array($value))) {
                $this->returnValue($seq, 0, $value);
            }
        };
        $this->ffi->webview_bind($this->webview, $name, $newFunction, null);

        return $this;
    }

    public function unbind($name): self
    {
        $this->ffi->webview_unbind($this->webview, $name);

        return $this;
    }

    public function eval(string $js): self
    {
        $this->ffi->webview_eval($this->webview, $js);

        return $this;
    }

    public function init(string $js): self
    {
        $this->ffi->webview_init($this->webview, $js);

        return $this;
    }

    public function navigate(string $url): self
    {
        $this->ffi->webview_navigate($this->webview, $url);

        return $this;
    }

    public function size(int $width, int $height, WindowSizeHint $hint): self
    {
        $this->width = $width;
        $this->height = $height;
        $this->hint = $hint;
        $this->ffi->webview_set_size($this->webview, $this->width, $this->height, $this->hint->value);
    }

    public function icon_title(string $title): self
    {
        $this->ffi->webview_notify_icon($this->webview, $title);
        return $this;
    }

    public function run(): self
    {
        $this->ffi->webview_set_title($this->webview, $this->title);
        $this->ffi->webview_run($this->webview);

        return $this;
    }

    public function destroy(): self
    {
        $this->ffi->webview_destroy($this->webview);

        return $this;
    }

    public function terminate(): self
    {
        $this->ffi->webview_terminate($this->webview);

        return $this;
    }

    public function show_win(): self
    {
        $this->ffi->webview_show_win($this->webview);
        return $this;
    }

    public function destroy_win(): self
    {
        $this->ffi->webview_destroy_win($this->webview);
        return $this;
    }

    /**
     * 任务栏图标菜单 function
     *
     * @param array $arr
     * @return self
     */
    public function icon_menu(array $arr): self
    {
        if (!count($arr)) {
            throw new \Exception("Cannot be an empty array");
        }
        $v = $this;
        $this->ffi->webview_icon_menu($this->webview, function () use ($v, $arr) {
            $hp = $v->ffi->webview_creat_icon_menu($v->webview);

            foreach ($arr as $k => $val) {
                if (isset($val["name"])) {
                    $v->ffi->webview_icon_menu_text($v->webview, $hp, (int)$k, $val["name"]);
                } else {
                    throw new \Exception("There is no field name in key $k");
                }
            }

            $num = $v->ffi->webview_track_icon_menu($v->webview, $hp);

            if (isset($arr[$num]["fn"]) && is_callable($arr[$num]["fn"])) {
                $arr[$num]["fn"]();
            } else {
                throw new \Exception("Field `fn` of key $num must be a function");
            }
            $v->ffi->webview_destory_icon_menu($v->webview, $hp);
        });
        return $this;
    }


    /**
     * @throws OsException
     */
    private function getDefaultLibraryFile(): string
    {
        if ($this->libraryFile !== null) {
            return $this->libraryFile;
        }
        $this->libraryFile = dirname(__DIR__)
            . DIRECTORY_SEPARATOR
            . "dll"
            . DIRECTORY_SEPARATOR
            . 'webview_php_ffi.dll';
        return $this->libraryFile;
    }
}
