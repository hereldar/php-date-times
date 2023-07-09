<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use ArithmeticError;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class SubtractionTest extends TestCase
{
    public function testSubtractYearsPositive(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1984, $dateTime->minus(years: 2)->year());
        self::assertSame(1984, $dateTime->minus(Period::of(years: 2))->year());
    }

    public function testSubtractYearsZero(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1986, $dateTime->minus(years: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(years: 0))->year());
    }

    public function testSubtractYearsNegative(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1988, $dateTime->minus(years: -2)->year());
        self::assertSame(1988, $dateTime->minus(Period::of(years: -2))->year());
    }

    public function testSubtractYearsOverflow(): void
    {
        $dateTime = DateTime::fromIso8601('2016-02-29T00:00:00Z');
        self::assertDateTime($dateTime->minus(years: 2), 2014, 2, 28);
        self::assertDateTime($dateTime->minus(Period::of(years: 2)), 2014, 2, 28);
        self::assertDateTime($dateTime->minus(years: 2, overflow: true), 2014, 3, 1);
        self::assertDateTime($dateTime->minus(Period::of(years: 2), overflow: true), 2014, 3, 1);

        $dateTime = DateTime::fromIso8601('2008-02-29T00:00:00Z');
        self::assertDateTime($dateTime->minus(years: 1, weeks: 1), 2007, 2, 21);
        self::assertDateTime($dateTime->minus(Period::of(years: 1, weeks: 1)), 2007, 2, 21);
        self::assertDateTime($dateTime->minus(years: 1, weeks: 1, overflow: true), 2007, 2, 22);
        self::assertDateTime($dateTime->minus(Period::of(years: 1, weeks: 1), overflow: true), 2007, 2, 22);
    }

    public function testSubtractMonthsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(7, $dateTime->minus(months: 2)->month());
        self::assertSame(7, $dateTime->minus(Period::of(months: 2))->month());
    }

    public function testSubtractMonthsZero(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(9, $dateTime->minus(months: 0)->month());
        self::assertSame(9, $dateTime->minus(Period::of(months: 0))->month());
    }

    public function testSubtractMonthsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(11, $dateTime->minus(months: -2)->month());
        self::assertSame(11, $dateTime->minus(Period::of(months: -2))->month());
    }

    public function testSubtractMonthsOverflow(): void
    {
        $dateTime = DateTime::fromIso8601('2021-03-31T00:00:00Z');
        self::assertDateTime($dateTime->minus(months: 1), 2021, 2, 28);
        self::assertDateTime($dateTime->minus(Period::of(months: 1)), 2021, 2, 28);
        self::assertDateTime($dateTime->minus(months: 1, overflow: true), 2021, 3, 3);
        self::assertDateTime($dateTime->minus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $dateTime = DateTime::fromIso8601('2007-03-31T00:00:00Z');
        self::assertDateTime($dateTime->minus(months: 1, weeks: 1), 2007, 2, 21);
        self::assertDateTime($dateTime->minus(Period::of(months: 1, weeks: 1)), 2007, 2, 21);
        self::assertDateTime($dateTime->minus(months: 1, weeks: 1, overflow: true), 2007, 2, 24);
        self::assertDateTime($dateTime->minus(Period::of(months: 1, weeks: 1), overflow: true), 2007, 2, 24);
    }

    public function testSubtractDaysPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(28, $dateTime->minus(days: 2)->day());
        self::assertSame(28, $dateTime->minus(Period::of(days: 2))->day());
    }

    public function testSubtractDaysZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(30, $dateTime->minus(days: 0)->day());
        self::assertSame(30, $dateTime->minus(Period::of(days: 0))->day());
    }

    public function testSubtractDaysNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(2, $dateTime->minus(days: -2)->day());
        self::assertSame(2, $dateTime->minus(Period::of(days: -2))->day());
    }

    public function testSubtractHoursPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(23, $dateTime->minus(hours: 1)->hour());
        self::assertSame(23, $dateTime->minus(Period::of(hours: 1))->hour());
    }

    public function testSubtractHoursZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(0, $dateTime->minus(hours: 0)->hour());
        self::assertSame(0, $dateTime->minus(Period::of(hours: 0))->hour());
    }

    public function testSubtractHoursNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(1, $dateTime->minus(hours: -1)->hour());
        self::assertSame(1, $dateTime->minus(Period::of(hours: -1))->hour());
    }

    public function testSubtractMinutesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(59, $dateTime->minus(minutes: 1)->minute());
        self::assertSame(59, $dateTime->minus(Period::of(minutes: 1))->minute());
    }

    public function testSubtractMinutesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(0, $dateTime->minus(minutes: 0)->minute());
        self::assertSame(0, $dateTime->minus(Period::of(minutes: 0))->minute());
    }

    public function testSubtractMinutesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(1, $dateTime->minus(minutes: -1)->minute());
        self::assertSame(1, $dateTime->minus(Period::of(minutes: -1))->minute());
    }

    public function testSubtractSecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(59, $dateTime->minus(seconds: 1)->second());
        self::assertSame(59, $dateTime->minus(Period::of(seconds: 1))->second());
    }

    public function testSubtractSecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(seconds: 0)->second());
        self::assertSame(0, $dateTime->minus(Period::of(seconds: 0))->second());
    }

    public function testSubtractSecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(seconds: -1)->second());
        self::assertSame(1, $dateTime->minus(Period::of(seconds: -1))->second());
    }

    public function testSubtractMicrosecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999999, $dateTime->minus(microseconds: 1)->microsecond());
        self::assertSame(999999, $dateTime->minus(Period::of(microseconds: 1))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99999, $dateTime->minus(microseconds: 1)->microsecond());
        self::assertSame(99999, $dateTime->minus(Period::of(microseconds: 1))->microsecond());
    }

    public function testSubtractMicrosecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(microseconds: 0)->microsecond());
        self::assertSame(0, $dateTime->minus(Period::of(microseconds: 0))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100000, $dateTime->minus(microseconds: 0)->microsecond());
        self::assertSame(100000, $dateTime->minus(Period::of(microseconds: 0))->microsecond());
    }

    public function testSubtractMicrosecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(microseconds: -1)->microsecond());
        self::assertSame(1, $dateTime->minus(Period::of(microseconds: -1))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100001, $dateTime->minus(microseconds: -1)->microsecond());
        self::assertSame(100001, $dateTime->minus(Period::of(microseconds: -1))->microsecond());
    }

    public function testSubtractMillenniaPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(986, $dateTime->minus(millennia: 1)->year());
        self::assertSame(986, $dateTime->minus(Period::of(millennia: 1))->year());
    }

    public function testSubtractMillenniaZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(millennia: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(millennia: 0))->year());
    }

    public function testSubtractMillenniaNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(2986, $dateTime->minus(millennia: -1)->year());
        self::assertSame(2986, $dateTime->minus(Period::of(millennia: -1))->year());
    }

    public function testSubtractCenturiesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1886, $dateTime->minus(centuries: 1)->year());
        self::assertSame(1886, $dateTime->minus(Period::of(centuries: 1))->year());
    }

    public function testSubtractCenturiesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(centuries: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(centuries: 0))->year());
    }

    public function testSubtractCenturiesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(2086, $dateTime->minus(centuries: -1)->year());
        self::assertSame(2086, $dateTime->minus(Period::of(centuries: -1))->year());
    }

    public function testSubtractDecadesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1976, $dateTime->minus(decades: 1)->year());
        self::assertSame(1976, $dateTime->minus(Period::of(decades: 1))->year());
    }

    public function testSubtractDecadesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(decades: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(decades: 0))->year());
    }

    public function testSubtractDecadesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1996, $dateTime->minus(decades: -1)->year());
        self::assertSame(1996, $dateTime->minus(Period::of(decades: -1))->year());
    }

    public function testSubtractQuartersPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(6, $dateTime->minus(quarters: 1)->month());
        self::assertSame(6, $dateTime->minus(Period::of(quarters: 1))->month());
    }

    public function testSubtractQuartersZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(9, $dateTime->minus(quarters: 0)->month());
        self::assertSame(9, $dateTime->minus(Period::of(quarters: 0))->month());
    }

    public function testSubtractQuartersNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(12, $dateTime->minus(quarters: -1)->month());
        self::assertSame(12, $dateTime->minus(Period::of(quarters: -1))->month());
    }

    public function testSubtractWeeksPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(14, $dateTime->minus(weeks: 1)->day());
        self::assertSame(14, $dateTime->minus(Period::of(weeks: 1))->day());
    }

    public function testSubtractWeeksZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(21, $dateTime->minus(weeks: 0)->day());
        self::assertSame(21, $dateTime->minus(Period::of(weeks: 0))->day());
    }

    public function testSubtractWeeksNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(28, $dateTime->minus(weeks: -1)->day());
        self::assertSame(28, $dateTime->minus(Period::of(weeks: -1))->day());
    }

    public function testSubtractMillisecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999, $dateTime->minus(milliseconds: 1)->millisecond());
        self::assertSame(999, $dateTime->minus(Period::of(milliseconds: 1))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99, $dateTime->minus(milliseconds: 1)->millisecond());
        self::assertSame(99, $dateTime->minus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testSubtractMillisecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(milliseconds: 0)->millisecond());
        self::assertSame(0, $dateTime->minus(Period::of(milliseconds: 0))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100, $dateTime->minus(milliseconds: 0)->millisecond());
        self::assertSame(100, $dateTime->minus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testSubtractMillisecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(milliseconds: -1)->millisecond());
        self::assertSame(1, $dateTime->minus(Period::of(milliseconds: -1))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(101, $dateTime->minus(milliseconds: -1)->millisecond());
        self::assertSame(101, $dateTime->minus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateTime::of(1986, 9, 25, 0, 0, 0)->minus(Period::of(1), 2);
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn () => DateTime::epoch()->minus(weeks: PHP_INT_MIN)
        );
        self::assertException(
            ArithmeticError::class,
            fn () => DateTime::epoch()->minus(microseconds: PHP_INT_MIN, milliseconds: -1)
        );
    }
}
