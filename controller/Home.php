<?php

declare(strict_types=1);

namespace app\controller;

class Home
{
    public function get(mixed $seq, mixed $req)
    {
        var_dump($seq);
        var_dump($req);
        return "ASd123";
    }
}
