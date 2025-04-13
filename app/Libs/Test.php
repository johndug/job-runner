<?php

namespace App\Libs;

use Illuminate\Support\Facades\Log;

class Test
{

    public function test(String $params): String
    {
        return $params;
    }

    public function test2(String $param, String $param2): String
    {
        return $param . " " . $param2;
    }

    public function test3(): String
    {
        return 'void';
    }

    public function test4(int $param): int
    {
        return $param;
    }
}
