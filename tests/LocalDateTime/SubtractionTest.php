<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use ArithmeticError;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class SubtractionTest extends TestCase
{
    public function testSubtractYearsPositive(): void
    {
        $dateTime = LocalDateTime::of(1986);
        self::assertSame(1984, $dateTime->minus(years: 2)->year());
        self::assertSame(1984, $dateTime->minus(Period::of(years: 2))->year());
    }

    public function testSubtractYearsZero(): void
    {
        $dateTime = LocalDateTime::of(1986);
        self::assertSame(1986, $dateTime->minus(years: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(years: 0))->year());
    }

    public function testSubtractYearsNegative(): void
    {
        $dateTime = LocalDateTime::of(1986);
        self::assertSame(1988, $dateTime->minus(years: -2)->year());
        self::assertSame(1988, $dateTime->minus(Period::of(years: -2))->year());
    }

    public function testSubtractYearsOverflow(): void
    {
        $dateTime = LocalDateTime::fromIso8601('2016-02-29T00:00:00');
        self::assertLocalDateTime($dateTime->minus(years: 2), 2014, 2, 28);
        self::assertLocalDateTime($dateTime->minus(Period::of(years: 2)), 2014, 2, 28);
        self::assertLocalDateTime($dateTime->minus(years: 2, overflow: true), 2014, 3, 1);
        self::assertLocalDateTime($dateTime->minus(Period::of(years: 2), overflow: true), 2014, 3, 1);

        $dateTime = LocalDateTime::fromIso8601('2008-02-29T00:00:00');
        self::assertLocalDateTime($dateTime->minus(years: 1, weeks: 1), 2007, 2, 21);
        self::assertLocalDateTime($dateTime->minus(Period::of(years: 1, weeks: 1)), 2007, 2, 21);
        self::assertLocalDateTime($dateTime->minus(years: 1, weeks: 1, overflow: true), 2007, 2, 22);
        self::assertLocalDateTime($dateTime->minus(Period::of(years: 1, weeks: 1), overflow: true), 2007, 2, 22);
    }

    public function testSubtractMonthsPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9);
        self::assertSame(7, $dateTime->minus(months: 2)->month());
        self::assertSame(7, $dateTime->minus(Period::of(months: 2))->month());
    }

    public function testSubtractMonthsZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9);
        self::assertSame(9, $dateTime->minus(months: 0)->month());
        self::assertSame(9, $dateTime->minus(Period::of(months: 0))->month());
    }

    public function testSubtractMonthsNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9);
        self::assertSame(11, $dateTime->minus(months: -2)->month());
        self::assertSame(11, $dateTime->minus(Period::of(months: -2))->month());
    }

    public function testSubtractMonthsOverflow(): void
    {
        $dateTime = LocalDateTime::fromIso8601('2021-03-31T00:00:00');
        self::assertLocalDateTime($dateTime->minus(months: 1), 2021, 2, 28);
        self::assertLocalDateTime($dateTime->minus(Period::of(months: 1)), 2021, 2, 28);
        self::assertLocalDateTime($dateTime->minus(months: 1, overflow: true), 2021, 3, 3);
        self::assertLocalDateTime($dateTime->minus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $dateTime = LocalDateTime::fromIso8601('2007-03-31T00:00:00');
        self::assertLocalDateTime($dateTime->minus(months: 1, weeks: 1), 2007, 2, 21);
        self::assertLocalDateTime($dateTime->minus(Period::of(months: 1, weeks: 1)), 2007, 2, 21);
        self::assertLocalDateTime($dateTime->minus(months: 1, weeks: 1, overflow: true), 2007, 2, 24);
        self::assertLocalDateTime($dateTime->minus(Period::of(months: 1, weeks: 1), overflow: true), 2007, 2, 24);
    }

    public function testSubtractDaysPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 30);
        self::assertSame(28, $dateTime->minus(days: 2)->day());
        self::assertSame(28, $dateTime->minus(Period::of(days: 2))->day());
    }

    public function testSubtractDaysZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 30);
        self::assertSame(30, $dateTime->minus(days: 0)->day());
        self::assertSame(30, $dateTime->minus(Period::of(days: 0))->day());
    }

    public function testSubtractDaysNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 30);
        self::assertSame(2, $dateTime->minus(days: -2)->day());
        self::assertSame(2, $dateTime->minus(Period::of(days: -2))->day());
    }

    public function testSubtractHoursPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0);
        self::assertSame(23, $dateTime->minus(hours: 1)->hour());
        self::assertSame(23, $dateTime->minus(Period::of(hours: 1))->hour());
    }

    public function testSubtractHoursZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0);
        self::assertSame(0, $dateTime->minus(hours: 0)->hour());
        self::assertSame(0, $dateTime->minus(Period::of(hours: 0))->hour());
    }

    public function testSubtractHoursNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0);
        self::assertSame(1, $dateTime->minus(hours: -1)->hour());
        self::assertSame(1, $dateTime->minus(Period::of(hours: -1))->hour());
    }

    public function testSubtractMinutesPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(59, $dateTime->minus(minutes: 1)->minute());
        self::assertSame(59, $dateTime->minus(Period::of(minutes: 1))->minute());
    }

    public function testSubtractMinutesZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(0, $dateTime->minus(minutes: 0)->minute());
        self::assertSame(0, $dateTime->minus(Period::of(minutes: 0))->minute());
    }

    public function testSubtractMinutesNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(1, $dateTime->minus(minutes: -1)->minute());
        self::assertSame(1, $dateTime->minus(Period::of(minutes: -1))->minute());
    }

    public function testSubtractSecondsPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(59, $dateTime->minus(seconds: 1)->second());
        self::assertSame(59, $dateTime->minus(Period::of(seconds: 1))->second());
    }

    public function testSubtractSecondsZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(seconds: 0)->second());
        self::assertSame(0, $dateTime->minus(Period::of(seconds: 0))->second());
    }

    public function testSubtractSecondsNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(seconds: -1)->second());
        self::assertSame(1, $dateTime->minus(Period::of(seconds: -1))->second());
    }

    public function testSubtractMicrosecondsPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999999, $dateTime->minus(microseconds: 1)->microsecond());
        self::assertSame(999999, $dateTime->minus(Period::of(microseconds: 1))->microsecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99999, $dateTime->minus(microseconds: 1)->microsecond());
        self::assertSame(99999, $dateTime->minus(Period::of(microseconds: 1))->microsecond());
    }

    public function testSubtractMicrosecondsZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(microseconds: 0)->microsecond());
        self::assertSame(0, $dateTime->minus(Period::of(microseconds: 0))->microsecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100000, $dateTime->minus(microseconds: 0)->microsecond());
        self::assertSame(100000, $dateTime->minus(Period::of(microseconds: 0))->microsecond());
    }

    public function testSubtractMicrosecondsNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(microseconds: -1)->microsecond());
        self::assertSame(1, $dateTime->minus(Period::of(microseconds: -1))->microsecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100001, $dateTime->minus(microseconds: -1)->microsecond());
        self::assertSame(100001, $dateTime->minus(Period::of(microseconds: -1))->microsecond());
    }

    public function testSubtractMillenniaPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(986, $dateTime->minus(millennia: 1)->year());
        self::assertSame(986, $dateTime->minus(Period::of(millennia: 1))->year());
    }

    public function testSubtractMillenniaZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(millennia: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(millennia: 0))->year());
    }

    public function testSubtractMillenniaNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(2986, $dateTime->minus(millennia: -1)->year());
        self::assertSame(2986, $dateTime->minus(Period::of(millennia: -1))->year());
    }

    public function testSubtractCenturiesPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1886, $dateTime->minus(centuries: 1)->year());
        self::assertSame(1886, $dateTime->minus(Period::of(centuries: 1))->year());
    }

    public function testSubtractCenturiesZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(centuries: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(centuries: 0))->year());
    }

    public function testSubtractCenturiesNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(2086, $dateTime->minus(centuries: -1)->year());
        self::assertSame(2086, $dateTime->minus(Period::of(centuries: -1))->year());
    }

    public function testSubtractDecadesPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1976, $dateTime->minus(decades: 1)->year());
        self::assertSame(1976, $dateTime->minus(Period::of(decades: 1))->year());
    }

    public function testSubtractDecadesZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->minus(decades: 0)->year());
        self::assertSame(1986, $dateTime->minus(Period::of(decades: 0))->year());
    }

    public function testSubtractDecadesNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(1996, $dateTime->minus(decades: -1)->year());
        self::assertSame(1996, $dateTime->minus(Period::of(decades: -1))->year());
    }

    public function testSubtractQuartersPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(6, $dateTime->minus(quarters: 1)->month());
        self::assertSame(6, $dateTime->minus(Period::of(quarters: 1))->month());
    }

    public function testSubtractQuartersZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(9, $dateTime->minus(quarters: 0)->month());
        self::assertSame(9, $dateTime->minus(Period::of(quarters: 0))->month());
    }

    public function testSubtractQuartersNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(12, $dateTime->minus(quarters: -1)->month());
        self::assertSame(12, $dateTime->minus(Period::of(quarters: -1))->month());
    }

    public function testSubtractWeeksPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(14, $dateTime->minus(weeks: 1)->day());
        self::assertSame(14, $dateTime->minus(Period::of(weeks: 1))->day());
    }

    public function testSubtractWeeksZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(21, $dateTime->minus(weeks: 0)->day());
        self::assertSame(21, $dateTime->minus(Period::of(weeks: 0))->day());
    }

    public function testSubtractWeeksNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21);
        self::assertSame(28, $dateTime->minus(weeks: -1)->day());
        self::assertSame(28, $dateTime->minus(Period::of(weeks: -1))->day());
    }

    public function testSubtractMillisecondsPositive(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999, $dateTime->minus(milliseconds: 1)->millisecond());
        self::assertSame(999, $dateTime->minus(Period::of(milliseconds: 1))->millisecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99, $dateTime->minus(milliseconds: 1)->millisecond());
        self::assertSame(99, $dateTime->minus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testSubtractMillisecondsZero(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->minus(milliseconds: 0)->millisecond());
        self::assertSame(0, $dateTime->minus(Period::of(milliseconds: 0))->millisecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100, $dateTime->minus(milliseconds: 0)->millisecond());
        self::assertSame(100, $dateTime->minus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testSubtractMillisecondsNegative(): void
    {
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->minus(milliseconds: -1)->millisecond());
        self::assertSame(1, $dateTime->minus(Period::of(milliseconds: -1))->millisecond());
        $dateTime = LocalDateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(101, $dateTime->minus(milliseconds: -1)->millisecond());
        self::assertSame(101, $dateTime->minus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        LocalDateTime::of(1986, 9, 25, 0, 0, 0)->minus(Period::of(1), 2);
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn() => LocalDateTime::epoch()->minus(weeks: \PHP_INT_MIN)
        );
        self::assertException(
            ArithmeticError::class,
            fn() => LocalDateTime::epoch()->minus(microseconds: \PHP_INT_MIN, milliseconds: -1)
        );
    }
}
