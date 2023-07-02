<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;

if (!function_exists('intadd')) {
    /**
     * @phpstan-pure
     * @psalm-pure
     */
    function intadd(int ...$nums): int
    {
        /** @var int|float $result */
        $result = array_sum($nums);

        if (is_int($result)) {
            return $result;
        }

        $num1 = (string) reset($nums);
        $num2 = (string) end($nums);

        if (count($nums) > 2) {
            $num2 = implode(', ', array_slice($nums, 1, -1))." and {$num2}";
        }

        throw new ArithmeticError("Addition of {$num1} plus {$num2} is not an integer");
    }
}

if (!function_exists('intsub')) {
    /**
     * @phpstan-pure
     * @psalm-pure
     */
    function intsub(int $num1, int $num2): int
    {
        /** @var int|float $result */
        $result = $num1 - $num2;

        if (is_int($result)) {
            return $result;
        }

        throw new ArithmeticError("Subtraction of {$num1} minus {$num2} is not an integer");
    }
}

if (!function_exists('intmul')) {
    /**
     * @phpstan-pure
     * @psalm-pure
     */
    function intmul(int $num1, int $num2): int
    {
        /** @var int|float $result */
        $result = $num1 * $num2;

        if (is_int($result)) {
            return $result;
        }

        throw new ArithmeticError("Multiplication of {$num1} by {$num2} is not an integer");
    }
}
