<?php

namespace App\Libs;

class TestClass
{

    public function testString(String $s): String
    {
        return sprintf("String: %s", $s);
    }

    public function testMultipleStrings(String $s1, String $s2, String $s3): String
    {
        return sprintf("String 1: %s, String 2: %s, String 3: %s", $s1, $s2, $s3);
    }

    public function isEven(int $number): bool
    {
        return $number % 2 === 0;
    }

    public function isOdd(int $number): bool
    {
        return $number % 2 !== 0;
    }
}
