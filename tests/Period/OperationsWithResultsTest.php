<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

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
    }

    public function testMultiplication(): void
    {
        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->multiplyBy(4)->orFail();
        self::assertPeriod($period, 16, 12, 76, 20, 40, 44);

        $period = Period::of(4, 3, 2, 5, 5, 10, 11)->multiplyBy(0)->orFail();
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0);

        $period = Period::of(-4, -3, -2, -5, -5, -10, -11)->multiplyBy(-1)->orFail();
        self::assertPeriod($period, 4, 3, 19, 5, 10, 11);
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
    }
}
