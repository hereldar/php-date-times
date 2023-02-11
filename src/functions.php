<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;

if (!function_exists('intadd')) {
    function intadd(int $num1, int $num2): int
    {
        $result = $num1 + $num2;

        if (is_float($result)) {
            throw new ArithmeticError("Addition of {$num1} plus {$num2} is not an integer");
        }

        return $result;
    }
}

if (!function_exists('intsub')) {
    function intsub(int $num1, int $num2): int
    {
        $result = $num1 - $num2;

        if (is_float($result)) {
            throw new ArithmeticError("Subtraction of {$num1} minus {$num2} is not an integer");
        }

        return $result;
    }
}

if (!function_exists('intmul')) {
    function intmul(int $num1, int $num2): int
    {
        $result = $num1 * $num2;

        if (is_float($result)) {
            throw new ArithmeticError("Multiplication of {$num1} by {$num1} is not an integer");
        }

        return $result;
    }
}
