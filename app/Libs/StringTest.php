<?php

namespace App\Libs;

class StringTest
{
    public function testString(string $s): string
    {
        return sprintf('String: %s', $s);
    }

    public function testMultipleStrings(string $s1, string $s2, string $s3): string
    {
        return sprintf('String 1: %s, String 2: %s, String 3: %s', $s1, $s2, $s3);
    }
}
