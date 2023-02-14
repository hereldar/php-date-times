<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class SubtractionTest extends TestCase
{
    public function testMinusMethod(): void
    {
        $date = LocalDate::of(1975)->minus(years: 2);
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame(1973, $date->year());
        $originalDate = LocalDate::fromIso8601('2020-06-04');
        $period = Period::of(days: 4);
        $date = $originalDate->minus($period);
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-05-31', $date->toIso8601());
        self::assertNotSame($date, $originalDate);
        $period = Period::of(days: 23);
        $date = LocalDate::fromIso8601('2020-06-23')->minus($period);
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-05-31', $date->toIso8601());
    }

    public function testAddYearsPositive(): void
    {
        $date = LocalDate::of(1975);
        self::assertSame(1973, $date->minus(years: 2)->year());
        self::assertSame(1973, $date->minus(Period::of(years: 2))->year());
    }

    public function testAddYearsZero(): void
    {
        $date = LocalDate::of(1975);
        self::assertSame(1975, $date->minus(years: 0)->year());
        self::assertSame(1975, $date->minus(Period::of(years: 0))->year());
    }

    public function testAddYearsNegative(): void
    {
        $date = LocalDate::of(1975);
        self::assertSame(1977, $date->minus(years: -2)->year());
        self::assertSame(1977, $date->minus(Period::of(years: -2))->year());
    }

    public function testAddYearsOverflow(): void
    {
        $date = LocalDate::of(2016, 2, 29);
        self::assertLocalDate($date->minus(years: 2), 2014, 3, 1);
        self::assertLocalDate($date->minus(Period::of(years: 2)), 2014, 3, 1);
    }

    public function testAddMonthsPositive(): void
    {
        $date = LocalDate::of(1975, 6);
        self::assertSame(4, $date->minus(months: 2)->month());
        self::assertSame(4, $date->minus(Period::of(months: 2))->month());
    }

    public function testAddMonthsZero(): void
    {
        $date = LocalDate::of(1975, 6);
        self::assertSame(6, $date->minus(months: 0)->month());
        self::assertSame(6, $date->minus(Period::of(months: 0))->month());
    }

    public function testAddMonthsNegative(): void
    {
        $date = LocalDate::of(1975, 6);
        self::assertSame(8, $date->minus(months: -2)->month());
        self::assertSame(8, $date->minus(Period::of(months: -2))->month());
    }

    public function testAddMonthsOverflow(): void
    {
        $date = LocalDate::fromIso8601('2021-03-31');
        self::assertLocalDate($date->minus(months: 1), 2021, 03, 03);
        self::assertLocalDate($date->minus(Period::of(months: 1)), 2021, 03, 03);
    }

    public function testAddWeeksPositive(): void
    {
        $date = LocalDate::of(1975, 5, 21);
        self::assertSame(14, $date->minus(weeks: 1)->day());
        self::assertSame(14, $date->minus(Period::of(weeks: 1))->day());
    }

    public function testAddWeeksZero(): void
    {
        $date = LocalDate::of(1975, 5, 21);
        self::assertSame(21, $date->minus(weeks: 0)->day());
        self::assertSame(21, $date->minus(Period::of(weeks: 0))->day());
    }

    public function testAddWeeksNegative(): void
    {
        $date = LocalDate::of(1975, 5, 21);
        self::assertSame(28, $date->minus(weeks: -1)->day());
        self::assertSame(28, $date->minus(Period::of(weeks: -1))->day());
    }

    public function testAddDaysPositive(): void
    {
        $date = LocalDate::of(1975, 5, 31);
        self::assertSame(29, $date->minus(days: 2)->day());
        self::assertSame(29, $date->minus(Period::of(days: 2))->day());
    }

    public function testAddDaysZero(): void
    {
        $date = LocalDate::of(1975, 5, 31);
        self::assertSame(31, $date->minus(days: 0)->day());
        self::assertSame(31, $date->minus(Period::of(days: 0))->day());
    }

    public function testAddDaysNegative(): void
    {
        $date = LocalDate::of(1975, 5, 31);
        self::assertSame(2, $date->minus(days: -2)->day());
        self::assertSame(2, $date->minus(Period::of(days: -2))->day());
    }
}
