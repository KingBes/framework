<?php

declare(strict_types=1);

return [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            // 数据库类型
            'type'        => 'sqlite',
            // 数据库名
            'database'    => app_path("sqlite3.db"),
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => '',
        ],
    ],
];
