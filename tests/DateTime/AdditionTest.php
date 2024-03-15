<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use ArithmeticError;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class AdditionTest extends TestCase
{
    public function testAddYearsPositive(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1988, $dateTime->plus(years: 2)->year());
        self::assertSame(1988, $dateTime->plus(Period::of(years: 2))->year());
    }

    public function testAddYearsZero(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1986, $dateTime->plus(years: 0)->year());
        self::assertSame(1986, $dateTime->plus(Period::of(years: 0))->year());
    }

    public function testAddYearsNegative(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1984, $dateTime->plus(years: -2)->year());
        self::assertSame(1984, $dateTime->plus(Period::of(years: -2))->year());
    }

    public function testAddYearsOverflow(): void
    {
        $dateTime = DateTime::fromIso8601('2016-02-29T00:00:00Z');
        self::assertDateTime($dateTime->plus(years: 2), 2018, 2, 28);
        self::assertDateTime($dateTime->plus(Period::of(years: 2)), 2018, 2, 28);
        self::assertDateTime($dateTime->plus(years: 2, overflow: true), 2018, 3, 1);
        self::assertDateTime($dateTime->plus(Period::of(years: 2), overflow: true), 2018, 3, 1);

        $dateTime = DateTime::fromIso8601('2008-02-29T00:00:00Z');
        self::assertDateTime($dateTime->plus(years: 1, days: 1), 2009, 3, 1);
        self::assertDateTime($dateTime->plus(Period::of(years: 1, days: 1)), 2009, 3, 1);
        self::assertDateTime($dateTime->plus(years: 1, days: 1, overflow: true), 2009, 3, 2);
        self::assertDateTime($dateTime->plus(Period::of(years: 1, days: 1), overflow: true), 2009, 3, 2);
    }

    public function testAddMonthsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(11, $dateTime->plus(months: 2)->month());
        self::assertSame(11, $dateTime->plus(Period::of(months: 2))->month());
    }

    public function testAddMonthsZero(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(9, $dateTime->plus(months: 0)->month());
        self::assertSame(9, $dateTime->plus(Period::of(months: 0))->month());
    }

    public function testAddMonthsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9);
        self::assertSame(7, $dateTime->plus(months: -2)->month());
        self::assertSame(7, $dateTime->plus(Period::of(months: -2))->month());
    }

    public function testAddMonthsOverflow(): void
    {
        $dateTime = DateTime::fromIso8601('2021-01-31T00:00:00Z');
        self::assertDateTime($dateTime->plus(months: 1), 2021, 2, 28);
        self::assertDateTime($dateTime->plus(Period::of(months: 1)), 2021, 2, 28);
        self::assertDateTime($dateTime->plus(months: 1, overflow: true), 2021, 3, 3);
        self::assertDateTime($dateTime->plus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $dateTime = DateTime::fromIso8601('2007-03-31T00:00:00Z');
        self::assertDateTime($dateTime->plus(months: 3, days: 1), 2007, 7, 1);
        self::assertDateTime($dateTime->plus(Period::of(months: 3, days: 1)), 2007, 7, 1);
        self::assertDateTime($dateTime->plus(months: 3, days: 1, overflow: true), 2007, 7, 2);
        self::assertDateTime($dateTime->plus(Period::of(months: 3, days: 1), overflow: true), 2007, 7, 2);
    }

    public function testAddDaysPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(2, $dateTime->plus(days: 2)->day());
        self::assertSame(2, $dateTime->plus(Period::of(days: 2))->day());
    }

    public function testAddDaysZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(30, $dateTime->plus(days: 0)->day());
        self::assertSame(30, $dateTime->plus(Period::of(days: 0))->day());
    }

    public function testAddDaysNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 30);
        self::assertSame(28, $dateTime->plus(days: -2)->day());
        self::assertSame(28, $dateTime->plus(Period::of(days: -2))->day());
    }

    public function testAddHoursPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(1, $dateTime->plus(hours: 1)->hour());
        self::assertSame(1, $dateTime->plus(Period::of(hours: 1))->hour());
    }

    public function testAddHoursZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(0, $dateTime->plus(hours: 0)->hour());
        self::assertSame(0, $dateTime->plus(Period::of(hours: 0))->hour());
    }

    public function testAddHoursNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0);
        self::assertSame(23, $dateTime->plus(hours: -1)->hour());
        self::assertSame(23, $dateTime->plus(Period::of(hours: -1))->hour());
    }

    public function testAddMinutesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(1, $dateTime->plus(minutes: 1)->minute());
        self::assertSame(1, $dateTime->plus(Period::of(minutes: 1))->minute());
    }

    public function testAddMinutesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(0, $dateTime->plus(minutes: 0)->minute());
        self::assertSame(0, $dateTime->plus(Period::of(minutes: 0))->minute());
    }

    public function testAddMinutesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(59, $dateTime->plus(minutes: -1)->minute());
        self::assertSame(59, $dateTime->plus(Period::of(minutes: -1))->minute());
    }

    public function testAddSecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->plus(seconds: 1)->second());
        self::assertSame(1, $dateTime->plus(Period::of(seconds: 1))->second());
    }

    public function testAddSecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->plus(seconds: 0)->second());
        self::assertSame(0, $dateTime->plus(Period::of(seconds: 0))->second());
    }

    public function testAddSecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(59, $dateTime->plus(seconds: -1)->second());
        self::assertSame(59, $dateTime->plus(Period::of(seconds: -1))->second());
    }

    public function testAddMicrosecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->plus(microseconds: 1)->microsecond());
        self::assertSame(1, $dateTime->plus(Period::of(microseconds: 1))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100001, $dateTime->plus(microseconds: 1)->microsecond());
        self::assertSame(100001, $dateTime->plus(Period::of(microseconds: 1))->microsecond());
    }

    public function testAddMicrosecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->plus(microseconds: 0)->microsecond());
        self::assertSame(0, $dateTime->plus(Period::of(microseconds: 0))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100000, $dateTime->plus(microseconds: 0)->microsecond());
        self::assertSame(100000, $dateTime->plus(Period::of(microseconds: 0))->microsecond());
    }

    public function testAddMicrosecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999999, $dateTime->plus(microseconds: -1)->microsecond());
        self::assertSame(999999, $dateTime->plus(Period::of(microseconds: -1))->microsecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99999, $dateTime->plus(microseconds: -1)->microsecond());
        self::assertSame(99999, $dateTime->plus(Period::of(microseconds: -1))->microsecond());
    }

    public function testAddMillenniaPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(2986, $dateTime->plus(millennia: 1)->year());
        self::assertSame(2986, $dateTime->plus(Period::of(millennia: 1))->year());
    }

    public function testAddMillenniaZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->plus(millennia: 0)->year());
        self::assertSame(1986, $dateTime->plus(Period::of(millennia: 0))->year());
    }

    public function testAddMillenniaNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(986, $dateTime->plus(millennia: -1)->year());
        self::assertSame(986, $dateTime->plus(Period::of(millennia: -1))->year());
    }

    public function testAddCenturiesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(2086, $dateTime->plus(centuries: 1)->year());
        self::assertSame(2086, $dateTime->plus(Period::of(centuries: 1))->year());
    }

    public function testAddCenturiesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->plus(centuries: 0)->year());
        self::assertSame(1986, $dateTime->plus(Period::of(centuries: 0))->year());
    }

    public function testAddCenturiesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1886, $dateTime->plus(centuries: -1)->year());
        self::assertSame(1886, $dateTime->plus(Period::of(centuries: -1))->year());
    }

    public function testAddDecadesPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1996, $dateTime->plus(decades: 1)->year());
        self::assertSame(1996, $dateTime->plus(Period::of(decades: 1))->year());
    }

    public function testAddDecadesZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1986, $dateTime->plus(decades: 0)->year());
        self::assertSame(1986, $dateTime->plus(Period::of(decades: 0))->year());
    }

    public function testAddDecadesNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(1976, $dateTime->plus(decades: -1)->year());
        self::assertSame(1976, $dateTime->plus(Period::of(decades: -1))->year());
    }

    public function testAddQuartersPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(12, $dateTime->plus(quarters: 1)->month());
        self::assertSame(12, $dateTime->plus(Period::of(quarters: 1))->month());
    }

    public function testAddQuartersZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(9, $dateTime->plus(quarters: 0)->month());
        self::assertSame(9, $dateTime->plus(Period::of(quarters: 0))->month());
    }

    public function testAddQuartersNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(6, $dateTime->plus(quarters: -1)->month());
        self::assertSame(6, $dateTime->plus(Period::of(quarters: -1))->month());
    }

    public function testAddWeeksPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(28, $dateTime->plus(weeks: 1)->day());
        self::assertSame(28, $dateTime->plus(Period::of(weeks: 1))->day());
    }

    public function testAddWeeksZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(21, $dateTime->plus(weeks: 0)->day());
        self::assertSame(21, $dateTime->plus(Period::of(weeks: 0))->day());
    }

    public function testAddWeeksNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21);
        self::assertSame(14, $dateTime->plus(weeks: -1)->day());
        self::assertSame(14, $dateTime->plus(Period::of(weeks: -1))->day());
    }

    public function testAddMillisecondsPositive(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $dateTime->plus(milliseconds: 1)->millisecond());
        self::assertSame(1, $dateTime->plus(Period::of(milliseconds: 1))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(101, $dateTime->plus(milliseconds: 1)->millisecond());
        self::assertSame(101, $dateTime->plus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testAddMillisecondsZero(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $dateTime->plus(milliseconds: 0)->millisecond());
        self::assertSame(0, $dateTime->plus(Period::of(milliseconds: 0))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100, $dateTime->plus(milliseconds: 0)->millisecond());
        self::assertSame(100, $dateTime->plus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testAddMillisecondsNegative(): void
    {
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999, $dateTime->plus(milliseconds: -1)->millisecond());
        self::assertSame(999, $dateTime->plus(Period::of(milliseconds: -1))->millisecond());
        $dateTime = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99, $dateTime->plus(milliseconds: -1)->millisecond());
        self::assertSame(99, $dateTime->plus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateTime::of(1986, 9, 25, 0, 0, 0)->plus(Period::of(1), 2);
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn () => DateTime::epoch()->plus(weeks: \PHP_INT_MAX)
        );
        self::assertException(
            ArithmeticError::class,
            fn () => DateTime::epoch()->plus(microseconds: \PHP_INT_MAX, milliseconds: 1)
        );
    }
}
