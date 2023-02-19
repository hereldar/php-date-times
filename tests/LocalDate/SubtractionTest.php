<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class SubtractionTest extends TestCase
{
    public function testSubtractYearsPositive(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1984, $date->minus(years: 2)->year());
        self::assertSame(1984, $date->minus(Period::of(years: 2))->year());
    }

    public function testSubtractYearsZero(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1986, $date->minus(years: 0)->year());
        self::assertSame(1986, $date->minus(Period::of(years: 0))->year());
    }

    public function testSubtractYearsNegative(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1988, $date->minus(years: -2)->year());
        self::assertSame(1988, $date->minus(Period::of(years: -2))->year());
    }

    public function testSubtractYearsOverflow(): void
    {
        $date = LocalDate::of(2016, 2, 29);
        self::assertLocalDate($date->minus(years: 2), 2014, 2, 28);
        self::assertLocalDate($date->minus(Period::of(years: 2)), 2014, 2, 28);
        self::assertLocalDate($date->minus(years: 2, overflow: true), 2014, 3, 1);
        self::assertLocalDate($date->minus(Period::of(years: 2), overflow: true), 2014, 3, 1);
    }

    public function testSubtractMonthsPositive(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(7, $date->minus(months: 2)->month());
        self::assertSame(7, $date->minus(Period::of(months: 2))->month());
    }

    public function testSubtractMonthsZero(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(9, $date->minus(months: 0)->month());
        self::assertSame(9, $date->minus(Period::of(months: 0))->month());
    }

    public function testSubtractMonthsNegative(): void
    {
        $date = LocalDate::of(1986, 9);
        self::assertSame(11, $date->minus(months: -2)->month());
        self::assertSame(11, $date->minus(Period::of(months: -2))->month());
    }

    public function testSubtractMonthsOverflow(): void
    {
        $date = LocalDate::fromIso8601('2021-03-31');
        self::assertLocalDate($date->minus(months: 1), 2021, 02, 28);
        self::assertLocalDate($date->minus(Period::of(months: 1)), 2021, 02, 28);
        self::assertLocalDate($date->minus(months: 1, overflow: true), 2021, 03, 03);
        self::assertLocalDate($date->minus(Period::of(months: 1), overflow: true), 2021, 03, 03);
    }

    public function testSubtractWeeksPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(14, $date->minus(weeks: 1)->day());
        self::assertSame(14, $date->minus(Period::of(weeks: 1))->day());
    }

    public function testSubtractWeeksZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(21, $date->minus(weeks: 0)->day());
        self::assertSame(21, $date->minus(Period::of(weeks: 0))->day());
    }

    public function testSubtractWeeksNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(28, $date->minus(weeks: -1)->day());
        self::assertSame(28, $date->minus(Period::of(weeks: -1))->day());
    }

    public function testSubtractDaysPositive(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(28, $date->minus(days: 2)->day());
        self::assertSame(28, $date->minus(Period::of(days: 2))->day());
    }

    public function testSubtractDaysZero(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(30, $date->minus(days: 0)->day());
        self::assertSame(30, $date->minus(Period::of(days: 0))->day());
    }

    public function testSubtractDaysNegative(): void
    {
        $date = LocalDate::of(1986, 9, 30);
        self::assertSame(2, $date->minus(days: -2)->day());
        self::assertSame(2, $date->minus(Period::of(days: -2))->day());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LocalDate::of(1986, 9, 25)->minus(Period::of(1), 2);
    }
}
