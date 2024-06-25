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
    function app(): object
    {
        $KingBes = app\KingBes::get_app();
        return $KingBes;
    }
}
