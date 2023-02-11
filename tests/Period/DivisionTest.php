<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use DivisionByZeroError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class DivisionTest extends TestCase
{
    public function testDividedByMoreThanOne(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->dividedBy(3);
        self::assertPeriod($period, 1, 5, 6, 9, 43, 23);
    }

    public function testDividedByOne(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->dividedBy(1);
        self::assertPeriod($period, 4, 3, 19, 5, 10, 11);
    }

    public function testDividedByZero(): void
    {
        $this->expectException(DivisionByZeroError::class);
        Period::of(4, 3, 2, 5, 5, 10, 11)->dividedBy(0);
    }

    public function testDividedByLessThanZero(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->dividedBy(-1);
        self::assertPeriod($period, -4, -3, -19, -5, -10, -11);
    }

    public function testDividedByLessThanZeroWithNegativePeriod(): void
    {
        $period = Period::of(-4, -3, -2, -5, -5, -10, -11)->dividedBy(-1);
        self::assertPeriod($period, 4, 3, 19, 5, 10, 11);
    }

    public function testArithmeticError(): void
    {
        $this->expectException(ArithmeticError::class);
        Period::of(PHP_INT_MIN)->dividedBy(-1);
    }
}
