<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class PeriodAdditionTest extends TestCase
{
    public function testPlusUnits(): void
    {
        $period = Period::of(years: 3)->plus(years: 4);
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->plus(months: 4);
        self::assertPeriod($period, 0, 7);

        $period = Period::of(weeks: 3)->plus(weeks: 4);
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(days: 3)->plus(days: 4);
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->plus(hours: 4);
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->plus(minutes: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->plus(seconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(milliseconds: 3)->plus(milliseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(microseconds: 3)->plus(microseconds: 4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(days: 3)->plus(weeks: 2, hours: 26);
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->plus(2, 1, 0, 5, 22, 33, 44);
        self::assertPeriod($period, 6, 4, 54, 30, 43, 55);
    }

    public function testPlusPeriods(): void
    {
        $period = Period::of(years: 3)->plus(Period::of(years: 4));
        self::assertPeriod($period, 7);

        $period = Period::of(months: 3)->plus(Period::of(months: 4));
        self::assertPeriod($period, 0, 7);

        $period = Period::of(weeks: 3)->plus(Period::of(weeks: 4));
        self::assertPeriod($period, 0, 0, 7 * 7);

        $period = Period::of(days: 3)->plus(Period::of(days: 4));
        self::assertPeriod($period, 0, 0, 7);

        $period = Period::of(hours: 3)->plus(Period::of(hours: 4));
        self::assertPeriod($period, 0, 0, 0, 7);

        $period = Period::of(minutes: 3)->plus(Period::of(minutes: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 7);

        $period = Period::of(seconds: 3)->plus(Period::of(seconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 7);

        $period = Period::of(milliseconds: 3)->plus(Period::of(milliseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7000);

        $period = Period::of(microseconds: 3)->plus(Period::of(microseconds: 4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);

        $period = Period::of(days: 3)->plus(Period::of(weeks: 2, hours: 26));
        self::assertPeriod($period, 0, 0, 17, 26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->plus(Period::of(2, 1, 0, 5, 22, 33, 44));
        self::assertPeriod($period, 6, 4, 54, 30, 43, 55);
    }

    public function testPlusNegativeUnits(): void
    {
        $period = Period::of(years: 3)->plus(years: -4);
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->plus(months: -4);
        self::assertPeriod($period, 0, -1);

        $period = Period::of(weeks: 3)->plus(weeks: -4);
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(days: 3)->plus(days: -4);
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->plus(hours: -4);
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->plus(minutes: -4);
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->plus(seconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(milliseconds: 3)->plus(milliseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(microseconds: 3)->plus(microseconds: -4);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(days: 3)->plus(weeks: -2, hours: -26);
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->plus(-2, -1, 0, -5, -22, -33, -44);
        self::assertPeriod($period, 2, 2, 44, -14, -23, -33);
    }

    public function testPlusNegativePeriods(): void
    {
        $period = Period::of(years: 3)->plus(Period::of(years: -4));
        self::assertPeriod($period, -1);

        $period = Period::of(months: 3)->plus(Period::of(months: -4));
        self::assertPeriod($period, 0, -1);

        $period = Period::of(weeks: 3)->plus(Period::of(weeks: -4));
        self::assertPeriod($period, 0, 0, -1 * 7);

        $period = Period::of(days: 3)->plus(Period::of(days: -4));
        self::assertPeriod($period, 0, 0, -1);

        $period = Period::of(hours: 3)->plus(Period::of(hours: -4));
        self::assertPeriod($period, 0, 0, 0, -1);

        $period = Period::of(minutes: 3)->plus(Period::of(minutes: -4));
        self::assertPeriod($period, 0, 0, 0, 0, -1);

        $period = Period::of(seconds: 3)->plus(Period::of(seconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, -1);

        $period = Period::of(milliseconds: 3)->plus(Period::of(milliseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1000);

        $period = Period::of(microseconds: 3)->plus(Period::of(microseconds: -4));
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, -1);

        $period = Period::of(days: 3)->plus(Period::of(weeks: -2, hours: -26));
        self::assertPeriod($period, 0, 0, -11, -26, 0, 0);

        $period = Period::of(4, 3, 6, 7, 8, 10, 11)->plus(Period::of(-2, -1, 0, -5, -22, -33, -44));
        self::assertPeriod($period, 2, 2, 44, -14, -23, -33);
    }
}
