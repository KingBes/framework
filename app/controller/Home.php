<?php

declare(strict_types=1);

namespace app\controller;

use think\Facade\Db;

class Home
{
    public function get(int $seq, array $req): mixed
    {
        // $DB = Db::name("asd")->select();
        var_dump($seq);
        var_dump($req);
        return "ASd123";
    }
}
