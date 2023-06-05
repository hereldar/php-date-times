<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use DivisionByZeroError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

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
            fn () => Period::of(PHP_INT_MAX)->add(1)->orFail()
        );

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(1)->add(PHP_INT_MAX)->orFail()
        );

        self::assertException(
            InvalidArgumentException::class,
            fn () => Period::zero()->add(Period::of(1), 2)
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
            fn () => Period::of(PHP_INT_MIN)->subtract(1)->orFail()
        );

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(-2)->subtract(PHP_INT_MAX)->orFail()
        );

        self::assertException(
            InvalidArgumentException::class,
            fn () => Period::zero()->subtract(Period::of(1), 2)
        );
    }

    public function testMultiplication(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->multiplyBy(4)->orFail();
        self::assertPeriod($period, 16, 12, 8, 20, 40, 44);

        $period = Period::of(4, 3, 2, 5, 10, 11)->multiplyBy(0)->orFail();
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0);

        $period = Period::of(-4, -3, -2, -5, -10, -11)->multiplyBy(-1)->orFail();
        self::assertPeriod($period, 4, 3, 2, 5, 10, 11);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MAX)->multiplyBy(2)->orFail()
        );
    }

    public function testDivision(): void
    {
        $period = Period::of(4, 3, 2, 5, 10, 11)->divideBy(3)->orFail();
        self::assertPeriod($period, 1, 5, 0, 17, 43, 23);

        self::assertException(
            DivisionByZeroError::class,
            fn () => Period::of(4, 3, 2, 5, 10, 11)->divideBy(0)->orFail()
        );

        $period = Period::of(-4, -3, -2, -5, -10, -11)->divideBy(-1)->orFail();
        self::assertPeriod($period, 4, 3, 2, 5, 10, 11);

        self::assertException(
            ArithmeticError::class,
            fn () => Period::of(PHP_INT_MIN)->divideBy(-1)->orFail()
        );
    }
}
