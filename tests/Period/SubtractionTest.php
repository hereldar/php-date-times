<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class SubtractionTest extends TestCase
{
    public function testMinusUnits(): void
    {
        $period = Period::of(years: 3)->minus(years: 4);
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->minus(months: 4);
        self::assertPeriod($period, 0, -1);

        $period = Period::of(weeks: 3)->minus(weeks: 4);
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(days: 3)->minus(days: 4);
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->minus(hours: 4);
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->minus(minutes: 4);
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->minus(seconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(milliseconds: 3)->minus(milliseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(microseconds: 3)->minus(microseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(days: 3)->minus(weeks: 2, hours: 26);
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->minus(2, 1, 0, 5, 22, 33, 44);
        self::assertPeriod($period, 2, 2, 44, -14, -23, -33);
    }

    public function testMinusPeriods(): void
    {
        $period = Period::of(years: 3)->minus(Period::of(years: 4));
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->minus(Period::of(months: 4));
        self::assertPeriod($period, 0, -1);

        $period = Period::of(weeks: 3)->minus(Period::of(weeks: 4));
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(days: 3)->minus(Period::of(days: 4));
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->minus(Period::of(hours: 4));
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->minus(Period::of(minutes: 4));
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->minus(Period::of(seconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(milliseconds: 3)->minus(Period::of(milliseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(microseconds: 3)->minus(Period::of(microseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(days: 3)->minus(Period::of(weeks: 2, hours: 26));
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->minus(Period::of(2, 1, 0, 5, 22, 33, 44));
        self::assertPeriod($period, 2, 2, 44, -14, -23, -33);
    }

    public function testMinusNegativeUnits(): void
    {
        $period = Period::of(years: 3)->minus(years: -4);
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->minus(months: -4);
        self::assertPeriod($period, 0, 7);

        $period = Period::of(weeks: 3)->minus(weeks: -4);
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(days: 3)->minus(days: -4);
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->minus(hours: -4);
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->minus(minutes: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->minus(seconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(milliseconds: 3)->minus(milliseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(microseconds: 3)->minus(microseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(days: 3)->minus(weeks: -2, hours: -26);
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->minus(-2, -1, -0, -5, -22, -33, -44);
        self::assertPeriod($period, 6, 4, 54, 30, 43, 55);
    }

    public function testMinusNegativePeriods(): void
    {
        $period = Period::of(years: 3)->minus(Period::of(years: -4));
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->minus(Period::of(months: -4));
        self::assertPeriod($period, 0, 7);

        $period = Period::of(weeks: 3)->minus(Period::of(weeks: -4));
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(days: 3)->minus(Period::of(days: -4));
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->minus(Period::of(hours: -4));
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->minus(Period::of(minutes: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->minus(Period::of(seconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(milliseconds: 3)->minus(Period::of(milliseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(microseconds: 3)->minus(Period::of(microseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(days: 3)->minus(Period::of(weeks: -2, hours: -26));
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->minus(Period::of(-2, -1, -0, -5, -22, -33, -44));
        self::assertPeriod($period, 6, 4, 54, 30, 43, 55);
    }

    public function testArithmeticError(): void
    {
        $this->expectException(ArithmeticError::class);
        Period::of(PHP_INT_MIN)->minus(Period::of(1));
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Period::zero()->minus(Period::of(1), 2);
    }
}
