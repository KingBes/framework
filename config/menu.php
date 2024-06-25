<?php

declare(strict_types=1);

// 小图标菜单
return [
    [
        "name" => "显示",
        "fn" => function () {
            app()->show_win();
        }
    ],
    [
        "name" => "退出",
        "fn" => function () {
            app()->destroy_win();
        }
    ]
];
