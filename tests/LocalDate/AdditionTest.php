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
        $date = LocalDate::of(2016, 2, 29);
        self::assertLocalDate($date->plus(years: 2), 2018, 3, 1);
        self::assertLocalDate($date->plus(Period::of(years: 2)), 2018, 3, 1);
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
        self::assertLocalDate($date->plus(months: 1), 2021, 03, 03);
        self::assertLocalDate($date->plus(Period::of(months: 1)), 2021, 03, 03);
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

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LocalDate::of(1986, 9, 25)->plus(Period::of(1), 2);
    }
}
