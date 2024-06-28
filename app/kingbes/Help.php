<?php

declare(strict_types=1);

if (!function_exists("base_path")) {
    /**
     * 根目录路径 function
     *
     * @param string ...$path 拼接
     * @return string
     */
    function base_path(string ...$path): string
    {
        $dir = dirname(dirname(__DIR__));
        foreach ($path as $k => $v) {
            $dir .= DIRECTORY_SEPARATOR . $v;
        }
        if (is_dir($dir)) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
}

if (!function_exists("app_path")) {
    /**
     * app目录路径 function
     *
     * @param string ...$path 拼接
     * @return string
     */
    function app_path(string ...$path): string
    {
        $dir = dirname(__DIR__);
        foreach ($path as $k => $v) {
            $dir .= DIRECTORY_SEPARATOR . $v;
        }
        if (is_dir($dir)) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        return $dir;
    }
}

if (!function_exists("app")) {
    /**
     * app应用 function
     *
     * @return object
     */
    function app(): object
    {
        $KingBes = app\kingbes\KingBes::get_app();
        return $KingBes;
    }
}

if (!function_exists("app_jump")) {
    /**
     * 页面跳转 function
     *
     * @param string $page
     * @return void
     */
    function app_jump(string $page): void
    {
        app\kingbes\KingBes::app_jump($page);
    }
}

if (!function_exists("dialog_msg")) {
    /**
     * 消息对话框 function
     *
     * @param string $str
     * @param integer $type
     * @return boolean
     */
    function dialog_msg(string $str, int $type = 0): bool
    {
        if ($type < 0 || $type > 2) {
            throw new Exception("The parameter 'type' must be between 0 and 2");
        }
        $msg = app\kingbes\KingBes::dialog()->msg($str, $type);
        return $msg;
    }
}

if (!function_exists("dialog_prompt")) {
    /**
     * 输入对话框 function
     *
     * @return string
     */
    function dialog_prompt(): string
    {
        $msg = app\kingbes\KingBes::dialog()->prompt();
        return $msg;
    }
}

if (!function_exists("dialog_file")) {
    /**
     * 打开文件对话框 function
     *
     * @return string
     */
    function dialog_file(): string
    {
        $path = app\kingbes\KingBes::dialog()->file();
        return $path;
    }
}

if (!function_exists("dialog_dir")) {
    /**
     * 打开文件夹对话框 function
     *
     * @param string $default_dir 默认文件夹路径位置
     * @return string
     */
    function dialog_dir(string $default_dir = ""): string
    {
        $path = app\kingbes\KingBes::dialog()->dir($default_dir);
        return $path;
    }
}

if (!function_exists("dialog_file")) {
    /**
     * 保存文件对话框 function
     *
     * @param string $content 内容
     * @param string $filename 文件名 如：test.txt
     * @param string $path 保存路径 如：D:/dir
     * @return boolean
     */
    function dialog_file(
        string $content,
        string $filename,
        string $path = ""
    ): bool {
        $res = app\kingbes\KingBes::dialog()
            ->save(
                $content,
                $filename,
                $path
            );
        return $res;
    }
}
