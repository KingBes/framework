<?php

require "vendor/autoload.php";

use KingBes\PhpWebview\WebView;
use KingBes\PhpWebview\Dialog;
use KingBes\PhpWebview\Toast;

/* // webview实例
$webview = new WebView('Php WebView', 640, 480, true);
// 获取html
$html = __DIR__ . DIRECTORY_SEPARATOR . "index.html";
// 设置navigate
$webview->navigate($html);

// 对话实例
$dialog = new Dialog();
// 绑定
$webview->bind('openMsg', function ($seq, $req, $context) use ($dialog) {
    // 弹出消息窗口
    $msg = $dialog->msg($req[0], $req[1]);
    return ["code" => 0, "msg" => $msg];
});

// 运行
$webview->run();
// 销毁
$webview->destroy(); */

$toast = new Toast();

$toast->Instance_Create()
    ->setAppName("toast")
    ->setAppUserModelId()
    ->setShortcutPolicy(0)
    ->initialize()
    ->Template_Create(4)
    ->Template_setFirstLine("php toast")
    ->showToast();
