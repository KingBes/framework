<?php

namespace bes;

enum WinSizeHint: int
{
    case HINT_NONE = 0;
    case HINT_MIN = 1;
    case HINT_MAX = 2;
    case HINT_FIXED = 3;
}
