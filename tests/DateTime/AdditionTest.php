<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class AdditionTest extends TestCase
{
    public function testAddYearsPositive(): void
    {
        $date = DateTime::of(1986);
        self::assertSame(1988, $date->plus(years: 2)->year());
        self::assertSame(1988, $date->plus(Period::of(years: 2))->year());
    }

    public function testAddYearsZero(): void
    {
        $date = DateTime::of(1986);
        self::assertSame(1986, $date->plus(years: 0)->year());
        self::assertSame(1986, $date->plus(Period::of(years: 0))->year());
    }

    public function testAddYearsNegative(): void
    {
        $date = DateTime::of(1986);
        self::assertSame(1984, $date->plus(years: -2)->year());
        self::assertSame(1984, $date->plus(Period::of(years: -2))->year());
    }

    public function testAddYearsOverflow(): void
    {
        $date = DateTime::fromIso8601('2016-02-29T00:00:00Z');
        self::assertDateTime($date->plus(years: 2), 2018, 2, 28);
        self::assertDateTime($date->plus(Period::of(years: 2)), 2018, 2, 28);
        self::assertDateTime($date->plus(years: 2, overflow: true), 2018, 3, 1);
        self::assertDateTime($date->plus(Period::of(years: 2), overflow: true), 2018, 3, 1);

        $date = DateTime::fromIso8601('2008-02-29T00:00:00Z');
        self::assertDateTime($date->plus(years: 1, days: 1), 2009, 3, 1);
        self::assertDateTime($date->plus(Period::of(years: 1, days: 1)), 2009, 3, 1);
        self::assertDateTime($date->plus(years: 1, days: 1, overflow: true), 2009, 3, 2);
        self::assertDateTime($date->plus(Period::of(years: 1, days: 1), overflow: true), 2009, 3, 2);
    }

    public function testAddMonthsPositive(): void
    {
        $date = DateTime::of(1986, 9);
        self::assertSame(11, $date->plus(months: 2)->month());
        self::assertSame(11, $date->plus(Period::of(months: 2))->month());
    }

    public function testAddMonthsZero(): void
    {
        $date = DateTime::of(1986, 9);
        self::assertSame(9, $date->plus(months: 0)->month());
        self::assertSame(9, $date->plus(Period::of(months: 0))->month());
    }

    public function testAddMonthsNegative(): void
    {
        $date = DateTime::of(1986, 9);
        self::assertSame(7, $date->plus(months: -2)->month());
        self::assertSame(7, $date->plus(Period::of(months: -2))->month());
    }

    public function testAddMonthsOverflow(): void
    {
        $date = DateTime::fromIso8601('2021-01-31T00:00:00Z');
        self::assertDateTime($date->plus(months: 1), 2021, 2, 28);
        self::assertDateTime($date->plus(Period::of(months: 1)), 2021, 2, 28);
        self::assertDateTime($date->plus(months: 1, overflow: true), 2021, 3, 3);
        self::assertDateTime($date->plus(Period::of(months: 1), overflow: true), 2021, 3, 3);

        $date = DateTime::fromIso8601('2007-03-31T00:00:00Z');
        self::assertDateTime($date->plus(months: 3, days: 1), 2007, 7, 1);
        self::assertDateTime($date->plus(Period::of(months: 3, days: 1)), 2007, 7, 1);
        self::assertDateTime($date->plus(months: 3, days: 1, overflow: true), 2007, 7, 2);
        self::assertDateTime($date->plus(Period::of(months: 3, days: 1), overflow: true), 2007, 7, 2);
    }

    public function testAddWeeksPositive(): void
    {
        $date = DateTime::of(1986, 9, 21);
        self::assertSame(28, $date->plus(weeks: 1)->day());
        self::assertSame(28, $date->plus(Period::of(weeks: 1))->day());
    }

    public function testAddWeeksZero(): void
    {
        $date = DateTime::of(1986, 9, 21);
        self::assertSame(21, $date->plus(weeks: 0)->day());
        self::assertSame(21, $date->plus(Period::of(weeks: 0))->day());
    }

    public function testAddWeeksNegative(): void
    {
        $date = DateTime::of(1986, 9, 21);
        self::assertSame(14, $date->plus(weeks: -1)->day());
        self::assertSame(14, $date->plus(Period::of(weeks: -1))->day());
    }

    public function testAddDaysPositive(): void
    {
        $date = DateTime::of(1986, 9, 30);
        self::assertSame(2, $date->plus(days: 2)->day());
        self::assertSame(2, $date->plus(Period::of(days: 2))->day());
    }

    public function testAddDaysZero(): void
    {
        $date = DateTime::of(1986, 9, 30);
        self::assertSame(30, $date->plus(days: 0)->day());
        self::assertSame(30, $date->plus(Period::of(days: 0))->day());
    }

    public function testAddDaysNegative(): void
    {
        $date = DateTime::of(1986, 9, 30);
        self::assertSame(28, $date->plus(days: -2)->day());
        self::assertSame(28, $date->plus(Period::of(days: -2))->day());
    }

    public function testAddHoursPositive(): void
    {
        $time = DateTime::of(1986, 9, 21, 0);
        self::assertSame(1, $time->plus(hours: 1)->hour());
        self::assertSame(1, $time->plus(Period::of(hours: 1))->hour());
    }

    public function testAddHoursZero(): void
    {
        $time = DateTime::of(1986, 9, 21, 0);
        self::assertSame(0, $time->plus(hours: 0)->hour());
        self::assertSame(0, $time->plus(Period::of(hours: 0))->hour());
    }

    public function testAddHoursNegative(): void
    {
        $time = DateTime::of(1986, 9, 21, 0);
        self::assertSame(23, $time->plus(hours: -1)->hour());
        self::assertSame(23, $time->plus(Period::of(hours: -1))->hour());
    }

    public function testAddMinutesPositive(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(1, $time->plus(minutes: 1)->minute());
        self::assertSame(1, $time->plus(Period::of(minutes: 1))->minute());
    }

    public function testAddMinutesZero(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(0, $time->plus(minutes: 0)->minute());
        self::assertSame(0, $time->plus(Period::of(minutes: 0))->minute());
    }

    public function testAddMinutesNegative(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0);
        self::assertSame(59, $time->plus(minutes: -1)->minute());
        self::assertSame(59, $time->plus(Period::of(minutes: -1))->minute());
    }

    public function testAddSecondsPositive(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $time->plus(seconds: 1)->second());
        self::assertSame(1, $time->plus(Period::of(seconds: 1))->second());
    }

    public function testAddSecondsZero(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $time->plus(seconds: 0)->second());
        self::assertSame(0, $time->plus(Period::of(seconds: 0))->second());
    }

    public function testAddSecondsNegative(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(59, $time->plus(seconds: -1)->second());
        self::assertSame(59, $time->plus(Period::of(seconds: -1))->second());
    }

    public function testAddMillisecondsPositive(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $time->plus(milliseconds: 1)->millisecond());
        self::assertSame(1, $time->plus(Period::of(milliseconds: 1))->millisecond());
        $time = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(101, $time->plus(milliseconds: 1)->millisecond());
        self::assertSame(101, $time->plus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testAddMillisecondsZero(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $time->plus(milliseconds: 0)->millisecond());
        self::assertSame(0, $time->plus(Period::of(milliseconds: 0))->millisecond());
        $time = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100, $time->plus(milliseconds: 0)->millisecond());
        self::assertSame(100, $time->plus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testAddMillisecondsNegative(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999, $time->plus(milliseconds: -1)->millisecond());
        self::assertSame(999, $time->plus(Period::of(milliseconds: -1))->millisecond());
        $time = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99, $time->plus(milliseconds: -1)->millisecond());
        self::assertSame(99, $time->plus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testAddMicrosecondsPositive(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(1, $time->plus(microseconds: 1)->microsecond());
        self::assertSame(1, $time->plus(Period::of(microseconds: 1))->microsecond());
        $time = DateTime::of(1986, 9, 21,  0, 0, 0, 100_000);
        self::assertSame(100001, $time->plus(microseconds: 1)->microsecond());
        self::assertSame(100001, $time->plus(Period::of(microseconds: 1))->microsecond());
    }

    public function testAddMicrosecondsZero(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(0, $time->plus(microseconds: 0)->microsecond());
        self::assertSame(0, $time->plus(Period::of(microseconds: 0))->microsecond());
        $time = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(100000, $time->plus(microseconds: 0)->microsecond());
        self::assertSame(100000, $time->plus(Period::of(microseconds: 0))->microsecond());
    }

    public function testAddMicrosecondsNegative(): void
    {
        $time = DateTime::of(1986, 9, 21, 0, 0, 0);
        self::assertSame(999999, $time->plus(microseconds: -1)->microsecond());
        self::assertSame(999999, $time->plus(Period::of(microseconds: -1))->microsecond());
        $time = DateTime::of(1986, 9, 21, 0, 0, 0, 100_000);
        self::assertSame(99999, $time->plus(microseconds: -1)->microsecond());
        self::assertSame(99999, $time->plus(Period::of(microseconds: -1))->microsecond());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateTime::of(1986, 9, 25, 0, 0, 0)->plus(Period::of(1), 2);
    }
}
