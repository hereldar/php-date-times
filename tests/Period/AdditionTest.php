<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class AdditionTest extends TestCase
{
    public function testPlusUnits(): void
    {
        $period = Period::of(years: 3)->plus(years: 4);
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->plus(months: 4);
        self::assertPeriod($period, 0, 7);

        $period = Period::of(days: 3)->plus(days: 4);
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->plus(hours: 4);
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->plus(minutes: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->plus(seconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(microseconds: 3)->plus(microseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(millennia: 3)->plus(millennia: 4);
        self::assertPeriod($period, 7_000, 0, 0);

        $period = Period::of(centuries: 3)->plus(centuries: 4);
        self::assertPeriod($period, 700, 0, 0);

        $period = Period::of(decades: 3)->plus(decades: 4);
        self::assertPeriod($period, 70, 0, 0);

        $period = Period::of(quarters: 3)->plus(quarters: 4);
        self::assertPeriod($period, 0, 7 * 3, 0);

        $period = Period::of(weeks: 3)->plus(weeks: 4);
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(milliseconds: 3)->plus(milliseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(days: 3)->plus(weeks: 2, hours: 26);
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 7, 8, 10, 11)->plus(2, 1, 5, 22, 33, 44);
        self::assertPeriod($period, 6, 4, 12, 30, 43, 55);
    }

    public function testPlusPeriods(): void
    {
        $period = Period::of(years: 3)->plus(Period::of(years: 4));
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->plus(Period::of(months: 4));
        self::assertPeriod($period, 0, 7);

        $period = Period::of(days: 3)->plus(Period::of(days: 4));
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->plus(Period::of(hours: 4));
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->plus(Period::of(minutes: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->plus(Period::of(seconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(microseconds: 3)->plus(Period::of(microseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(millennia: 3)->plus(Period::of(millennia: 4));
        self::assertPeriod($period, 7_000, 0, 0);

        $period = Period::of(centuries: 3)->plus(Period::of(centuries: 4));
        self::assertPeriod($period, 700, 0, 0);

        $period = Period::of(decades: 3)->plus(Period::of(decades: 4));
        self::assertPeriod($period, 70, 0, 0);

        $period = Period::of(quarters: 3)->plus(Period::of(quarters: 4));
        self::assertPeriod($period, 0, 7 * 3, 0);

        $period = Period::of(weeks: 3)->plus(Period::of(weeks: 4));
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(milliseconds: 3)->plus(Period::of(milliseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(days: 3)->plus(Period::of(weeks: 2, hours: 26));
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 7, 8, 10, 11)->plus(Period::of(2, 1, 5, 22, 33, 44));
        self::assertPeriod($period, 6, 4, 12, 30, 43, 55);
    }

    public function testPlusNegativeUnits(): void
    {
        $period = Period::of(years: 3)->plus(years: -4);
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->plus(months: -4);
        self::assertPeriod($period, 0, -1);

        $period = Period::of(days: 3)->plus(days: -4);
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->plus(hours: -4);
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->plus(minutes: -4);
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->plus(seconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(microseconds: 3)->plus(microseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(millennia: 3)->plus(millennia: -4);
        self::assertPeriod($period, -1_000, 0, 0);

        $period = Period::of(centuries: 3)->plus(centuries: -4);
        self::assertPeriod($period, -100, 0, 0);

        $period = Period::of(decades: 3)->plus(decades: -4);
        self::assertPeriod($period, -10, 0, 0);

        $period = Period::of(quarters: 3)->plus(quarters: -4);
        self::assertPeriod($period, 0, -1 * 3, 0);

        $period = Period::of(weeks: 3)->plus(weeks: -4);
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(milliseconds: 3)->plus(milliseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(days: 3)->plus(weeks: -2, hours: -26);
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 7, 8, 10, 11)->plus(-2, -1, -5, -22, -33, -44);
        self::assertPeriod($period, 2, 2, 2, -14, -23, -33);
    }

    public function testPlusNegativePeriods(): void
    {
        $period = Period::of(years: 3)->plus(Period::of(years: -4));
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->plus(Period::of(months: -4));
        self::assertPeriod($period, 0, -1);

        $period = Period::of(days: 3)->plus(Period::of(days: -4));
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->plus(Period::of(hours: -4));
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->plus(Period::of(minutes: -4));
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->plus(Period::of(seconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(microseconds: 3)->plus(Period::of(microseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(millennia: 3)->plus(Period::of(millennia: -4));
        self::assertPeriod($period, -1_000, 0, 0);

        $period = Period::of(centuries: 3)->plus(Period::of(centuries: -4));
        self::assertPeriod($period, -100, 0, 0);

        $period = Period::of(decades: 3)->plus(Period::of(decades: -4));
        self::assertPeriod($period, -10, 0, 0);

        $period = Period::of(quarters: 3)->plus(Period::of(quarters: -4));
        self::assertPeriod($period, 0, -1 * 3, 0);

        $period = Period::of(weeks: 3)->plus(Period::of(weeks: -4));
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(milliseconds: 3)->plus(Period::of(milliseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(days: 3)->plus(Period::of(weeks: -2, hours: -26));
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 7, 8, 10, 11)->plus(Period::of(-2, -1, -5, -22, -33, -44));
        self::assertPeriod($period, 2, 2, 2, -14, -23, -33);
    }

    public function testArithmeticError(): void
    {
        $this->expectException(ArithmeticError::class);
        Period::of(PHP_INT_MAX)->plus(Period::of(1));
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Period::zero()->plus(Period::of(1), 2);
    }
}
