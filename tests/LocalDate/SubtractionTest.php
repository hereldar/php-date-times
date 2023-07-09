<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use ArithmeticError;
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
        $date = LocalDate::fromIso8601('2016-02-29');
        self::assertLocalDate($date->minus(years: 2), 2014, 2, 28);
        self::assertLocalDate($date->minus(Period::of(years: 2)), 2014, 2, 28);
        self::assertLocalDate($date->minus(years: 2, overflow: true), 2014, 3, 1);
        self::assertLocalDate($date->minus(Period::of(years: 2), overflow: true), 2014, 3, 1);

        $date = LocalDate::fromIso8601('2008-02-29');
        self::assertLocalDate($date->minus(years: 1, weeks: 1), 2007, 2, 21);
        self::assertLocalDate($date->minus(Period::of(years: 1, weeks: 1)), 2007, 2, 21);
        self::assertLocalDate($date->minus(years: 1, weeks: 1, overflow: true), 2007, 2, 22);
        self::assertLocalDate($date->minus(Period::of(years: 1, weeks: 1), overflow: true), 2007, 2, 22);
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
        self::assertLocalDate($date->minus(months: 1), 2021, 2, 28);
        self::assertLocalDate($date->minus(Period::of(months: 1)), 2021, 2, 28);
        self::assertLocalDate($date->minus(months: 1, overflow: true), 2021, 3, 3);
        self::assertLocalDate($date->minus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $date = LocalDate::fromIso8601('2007-03-31');
        self::assertLocalDate($date->minus(months: 1, weeks: 1), 2007, 2, 21);
        self::assertLocalDate($date->minus(Period::of(months: 1, weeks: 1)), 2007, 2, 21);
        self::assertLocalDate($date->minus(months: 1, weeks: 1, overflow: true), 2007, 2, 24);
        self::assertLocalDate($date->minus(Period::of(months: 1, weeks: 1), overflow: true), 2007, 2, 24);
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

    public function testSubtractMillenniaPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(986, $date->minus(millennia: 1)->year());
        self::assertSame(986, $date->minus(Period::of(millennia: 1))->year());
    }

    public function testSubtractMillenniaZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->minus(millennia: 0)->year());
        self::assertSame(1986, $date->minus(Period::of(millennia: 0))->year());
    }

    public function testSubtractMillenniaNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(2986, $date->minus(millennia: -1)->year());
        self::assertSame(2986, $date->minus(Period::of(millennia: -1))->year());
    }

    public function testSubtractCenturiesPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1886, $date->minus(centuries: 1)->year());
        self::assertSame(1886, $date->minus(Period::of(centuries: 1))->year());
    }

    public function testSubtractCenturiesZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->minus(centuries: 0)->year());
        self::assertSame(1986, $date->minus(Period::of(centuries: 0))->year());
    }

    public function testSubtractCenturiesNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(2086, $date->minus(centuries: -1)->year());
        self::assertSame(2086, $date->minus(Period::of(centuries: -1))->year());
    }

    public function testSubtractDecadesPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1976, $date->minus(decades: 1)->year());
        self::assertSame(1976, $date->minus(Period::of(decades: 1))->year());
    }

    public function testSubtractDecadesZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1986, $date->minus(decades: 0)->year());
        self::assertSame(1986, $date->minus(Period::of(decades: 0))->year());
    }

    public function testSubtractDecadesNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(1996, $date->minus(decades: -1)->year());
        self::assertSame(1996, $date->minus(Period::of(decades: -1))->year());
    }

    public function testSubtractQuartersPositive(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(6, $date->minus(quarters: 1)->month());
        self::assertSame(6, $date->minus(Period::of(quarters: 1))->month());
    }

    public function testSubtractQuartersZero(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(9, $date->minus(quarters: 0)->month());
        self::assertSame(9, $date->minus(Period::of(quarters: 0))->month());
    }

    public function testSubtractQuartersNegative(): void
    {
        $date = LocalDate::of(1986, 9, 21);
        self::assertSame(12, $date->minus(quarters: -1)->month());
        self::assertSame(12, $date->minus(Period::of(quarters: -1))->month());
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

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a period is passed'),
            fn () => LocalDate::of(1986, 9, 25)->minus(Period::of(1), 2)
        );
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn () => LocalDate::epoch()->minus(weeks: PHP_INT_MIN)
        );
        self::assertException(
            ArithmeticError::class,
            fn () => LocalDate::epoch()->minus(days: PHP_INT_MIN, weeks: -1)
        );
    }
}
