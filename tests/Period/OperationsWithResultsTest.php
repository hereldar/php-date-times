<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use DivisionByZeroError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $period = Period::of(years: 3)->add(years: 4)->orFail();
        self::assertPeriod($period, 7);

        $period = Period::of(years: 3)->add(Period::of(years: 4))->orFail();
        self::assertPeriod($period, 7);

        $period = Period::of(years: 3)->add(years: -4)->orFail();
        self::assertPeriod($period, -1);

        $period = Period::of(years: 3)->add(Period::of(years: -4))->orFail();
        self::assertPeriod($period, -1);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MAX)->add(Period::of(1))->orFail()
        );
    }

    public function testSubtraction(): void
    {
        $period = Period::of(years: 3)->subtract(years: 4)->orFail();
        self::assertPeriod($period, -1);

        $period = Period::of(years: 3)->subtract(Period::of(years: 4))->orFail();
        self::assertPeriod($period, -1);

        $period = Period::of(years: 3)->subtract(years: -4)->orFail();
        self::assertPeriod($period, 7);

        $period = Period::of(years: 3)->subtract(Period::of(years: -4))->orFail();
        self::assertPeriod($period, 7);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MIN)->subtract(Period::of(1))->orFail()
        );
    }

    public function testMultiplication(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->multiplyBy(4)->orFail();
        self::assertPeriod($period, 16, 12, 76, 20, 40, 44);

        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->multiplyBy(0)->orFail();
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0);

        $period = Period::of(-4, -3, -2, -5, -5, -10, -11)->multiplyBy(-1)->orFail();
        self::assertPeriod($period, 4, 3, 19, 5, 10, 11);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MAX)->multiplyBy(2)->orFail()
        );
    }

    public function testDivision(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->divideBy(3)->orFail();
        self::assertPeriod($period, 1, 5, 6, 9, 43, 23);

        self::assertException(
            DivisionByZeroError::class,
            fn () => Period::of(4, 3, 2, 5, 5, 10, 11)->divideBy(0)->orFail()
        );

        $period = Period::of(-4, -3, -2, -5, -5, -10, -11)->divideBy(-1)->orFail();
        self::assertPeriod($period, 4, 3, 19, 5, 10, 11);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MIN)->divideBy(-1)->orFail()
        );
    }
}
