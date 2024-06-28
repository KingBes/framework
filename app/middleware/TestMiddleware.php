<?php

declare(strict_types=1);

namespace app\middleware;

class TestMiddleware
{
    public function process(string $page, object $next_class): bool
    {
        var_dump("下一页的页面");
        var_dump($page);
        var_dump("下一页的class");
        var_dump($next_class);
        // true 会放行 , false不放行
        return true;
    }
}
