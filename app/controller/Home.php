<?php

declare(strict_types=1);

namespace app\controller;

class Home
{
    public function get(int $seq, array $req): mixed
    {
        var_dump($seq);
        var_dump($req);
        return "ASd123";
    }
}
