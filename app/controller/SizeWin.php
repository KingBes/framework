<?php

declare(strict_types=1);

namespace app\controller;

use app\kingbes\PhpWebview\WindowSizeHint;

class SizeWin
{
    /**
     * 改变当前窗口的宽度 variable
     *
     * @var integer
     */
    public int $width = 800;
    /**
     * 改变当前窗口的高度 variable
     *
     * @var integer
     */
    public int $height = 800;
    /**
     * 改变当前窗口的提示 variable
     *
     * @var WindowSizeHint
     */
    public WindowSizeHint $hint = WindowSizeHint::HINT_FIXED;


}
