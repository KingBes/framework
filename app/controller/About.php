<?php

declare(strict_types=1);

namespace app\controller;

class About
{
    public function set(int $seq, array $req): array
    {
        var_dump($seq);
        var_dump($req);
        return ["你好"];
    }

    public function onExit(int $seq, array $req): array
    {
        app()->destroy_win();
        return [1];
    }
}
