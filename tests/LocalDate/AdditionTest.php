<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class AdditionTest extends TestCase
{
    public function testAddYearsPositive(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1988, $date->plus(years: 2)->year());
        self::assertSame(1988, $date->plus(Period::of(years: 2))->year());
    }

    public function testAddYearsZero(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1986, $date->plus(years: 0)->year());
        self::assertSame(1986, $date->plus(Period::of(years: 0))->year());
    }

    public function testAddYearsNegative(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1984, $date->plus(years: -2)->year());
        self::assertSame(1984, $date->plus(Period::of(years: -2))->year());
    }

    public function testAddYearsOverflow(): void
    {
        $date = LocalDate::fromIso8601('2016-02-29');
        self::assertLocalDate($date->plus(years: 2), 2018, 2, 28);
        self::assertLocalDate($date->plus(Period::of(years: 2)), 2018, 2, 28);
        self::assertLocalDate($date->plus(years: 2, overflow: true), 2018, 3, 1);
        self::assertLocalDate($date->plus(Period::of(years: 2), overflow: true), 2018, 3, 1);

        $date = LocalDate::fromIso8601('2008-02-29');
        self::assertLocalDate($date->plus(years: 1, days: 1), 2009, 3, 1);
        self::assertLocalDate($date->plus(Period::of(years: 1, days: 1)), 2009, 3, 1);
        self::assertLocalDate($date->plus(years: 1, days: 1, overflow: true), 2009, 3, 2);
        self::assertLocalDate($date->plus(Period::of(years: 1, days: 1), overflow: true), 2009, 3, 2);
    }

    public function testAddMonthsPositive(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(11, $date->plus(months: 2)->month());
        self::assertSame(11, $date->plus(Period::of(months: 2))->month());
    }

    public function testAddMonthsZero(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(9, $date->plus(months: 0)->month());
        self::assertSame(9, $date->plus(Period::of(months: 0))->month());
    }

    public function testAddMonthsNegative(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(7, $date->plus(months: -2)->month());
        self::assertSame(7, $date->plus(Period::of(months: -2))->month());
    }

    public function testAddMonthsOverflow(): void
    {
        $date = LocalDate::fromIso8601('2021-01-31');
        self::assertLocalDate($date->plus(months: 1), 2021, 2, 28);
        self::assertLocalDate($date->plus(Period::of(months: 1)), 2021, 2, 28);
        self::assertLocalDate($date->plus(months: 1, overflow: true), 2021, 3, 3);
        self::assertLocalDate($date->plus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $date = LocalDate::fromIso8601('2007-03-31');
        self::assertLocalDate($date->plus(months: 3, days: 1), 2007, 7, 1);
        self::assertLocalDate($date->plus(Period::of(months: 3, days: 1)), 2007, 7, 1);
        self::assertLocalDate($date->plus(months: 3, days: 1, overflow: true), 2007, 7, 2);
        self::assertLocalDate($date->plus(Period::of(months: 3, days: 1), overflow: true), 2007, 7, 2);
    }

    public function testAddDaysPositive(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(2, $date->plus(days: 2)->day());
        self::assertSame(2, $date->plus(Period::of(days: 2))->day());
    }

    public function testAddDaysZero(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(30, $date->plus(days: 0)->day());
        self::assertSame(30, $date->plus(Period::of(days: 0))->day());
    }

    public function testAddDaysNegative(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(28, $date->plus(days: -2)->day());
        self::assertSame(28, $date->plus(Period::of(days: -2))->day());
    }

    public function testAddWeeksPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(28, $date->plus(weeks: 1)->day());
        self::assertSame(28, $date->plus(Period::of(weeks: 1))->day());
    }

    public function testAddWeeksZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(21, $date->plus(weeks: 0)->day());
        self::assertSame(21, $date->plus(Period::of(weeks: 0))->day());
    }

    public function testAddWeeksNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(14, $date->plus(weeks: -1)->day());
        self::assertSame(14, $date->plus(Period::of(weeks: -1))->day());
    }

    public function testAddQuartersPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(12, $date->plus(quarters: 1)->month());
        self::assertSame(12, $date->plus(Period::of(quarters: 1))->month());
    }

    public function testAddQuartersZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(9, $date->plus(quarters: 0)->month());
        self::assertSame(9, $date->plus(Period::of(quarters: 0))->month());
    }

    public function testAddQuartersNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(6, $date->plus(quarters: -1)->month());
        self::assertSame(6, $date->plus(Period::of(quarters: -1))->month());
    }

    public function testAddDecadesPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1996, $date->plus(decades: 1)->year());
        self::assertSame(1996, $date->plus(Period::of(decades: 1))->year());
    }

    public function testAddDecadesZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->plus(decades: 0)->year());
        self::assertSame(1986, $date->plus(Period::of(decades: 0))->year());
    }

    public function testAddDecadesNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1976, $date->plus(decades: -1)->year());
        self::assertSame(1976, $date->plus(Period::of(decades: -1))->year());
    }

    public function testAddCenturiesPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(2086, $date->plus(centuries: 1)->year());
        self::assertSame(2086, $date->plus(Period::of(centuries: 1))->year());
    }

    public function testAddCenturiesZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->plus(centuries: 0)->year());
        self::assertSame(1986, $date->plus(Period::of(centuries: 0))->year());
    }

    public function testAddCenturiesNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1886, $date->plus(centuries: -1)->year());
        self::assertSame(1886, $date->plus(Period::of(centuries: -1))->year());
    }

    public function testAddMillenniaPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(2986, $date->plus(millennia: 1)->year());
        self::assertSame(2986, $date->plus(Period::of(millennia: 1))->year());
    }

    public function testAddMillenniaZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->plus(millennia: 0)->year());
        self::assertSame(1986, $date->plus(Period::of(millennia: 0))->year());
    }

    public function testAddMillenniaNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(986, $date->plus(millennia: -1)->year());
        self::assertSame(986, $date->plus(Period::of(millennia: -1))->year());
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a period is passed'),
            fn () => LocalDate::of(1986, 9, 25)->plus(Period::of(1), 2)
        );
    }
}
