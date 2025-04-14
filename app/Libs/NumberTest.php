<?php

namespace App\Libs;

class NumberTest
{
    public function isEven(int $number): bool
    {
        return $number % 2 === 0;
    }

    public function isOdd(int $number): bool
    {
        return $number % 2 !== 0;
    }

    public function isPrime(int $number): bool
    {
        if ($number <= 1) {
            return false;
        }

        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i === 0) {
                return false;
            }
        }

        return true;
    }

    public function isFibonacci(int $number): bool
    {
        if ($number < 0) {
            return false;
        }

        $a = 0;
        $b = 1;

        while ($b < $number) {
            $temp = $b;
            $b = $a + $b;
            $a = $temp;
        }

        return $b === $number;
    }
}