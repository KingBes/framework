<?php

declare(strict_types=1);

namespace app\controller;

use think\Facade\Db;

class Home
{
    /**
     * get function
     *
     * @param integer $seq 触发次数
     * @param array $req js传来的参数
     * @return array
     */
    public function get(int $seq, array $req): array
    {
        $DB = Db::name("text")->select();
        var_dump($DB->toArray());
        var_dump($seq);
        var_dump($req);
        return ["PHP WINDOWS GUI"];
    }
}
