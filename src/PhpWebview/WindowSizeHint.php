<?php

declare(strict_types=1);

namespace KingBes\PhpWebview;

/**
 * HINT_NONE 自由缩放
 * HINT_MIN 固定最小
 * HINT_MAX 固定最大
 * HINT_FIXED 禁止缩放
 */
enum WindowSizeHint: int
{
    case HINT_NONE = 0;
    case HINT_MIN = 1;
    case HINT_MAX = 2;
    case HINT_FIXED = 3;
}
