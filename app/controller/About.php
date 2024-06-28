<?php

declare(strict_types=1);

namespace app\controller;

class About
{
    public function set(int $seq, array $req): mixed
    {
        var_dump($seq);
        var_dump($req);
        return "ASd123";
    }

    public function onExit(int $seq, array $req): mixed
    {
        app()->destroy_win();
        return true;
    }
}
