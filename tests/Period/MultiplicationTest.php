<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class MultiplicationTest extends TestCase
{
    public function testMultipliedByMoreThanOne(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->multipliedBy(4);
        self::assertPeriod($period, 16, 12, 8, 20, 40, 44);
    }

    public function testMultipliedByOne(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->multipliedBy(1);
        self::assertPeriod($period, 4, 3, 2, 5, 10, 11);
    }

    public function testMultipliedByZero(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->multipliedBy(0);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0);
    }

    public function testMultipliedByLessThanZero(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->multipliedBy(-1);
        self::assertPeriod($period, -4, -3, -2, -5, -10, -11);
    }

    public function testMultipliedByLessThanZeroWithNegativePeriod(): void
    {
        $period = Period::of(-4, -3, -2, -5, -10, -11)->multipliedBy(-1);
        self::assertPeriod($period, 4, 3, 2, 5, 10, 11);
    }

    public function testArithmeticError(): void
    {
        $this->expectException(ArithmeticError::class);
        Period::of(PHP_INT_MAX)->multipliedBy(2);
    }
}
