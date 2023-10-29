<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;
use OutOfRangeException;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = DateTime::of(year: 10);
        $two = $one->with(year: 3);
        self::assertDateTime($one, 10, 1, 1, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 3, 1, 1, 0, 0, 0, 0, 'UTC');
    }

    public function testMonth(): void
    {
        $one = DateTime::of(month: 10);
        $two = $one->with(month: 3);
        self::assertDateTime($one, 1970, 10, 1, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 3, 1, 0, 0, 0, 0, 'UTC');
    }

    public function testInvalidMonths(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn() => $dateTime->with(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn() => $dateTime->with(month: 13)
        );
    }

    public function testDay(): void
    {
        $one = DateTime::of(day: 10);
        $two = $one->with(day: 3);
        self::assertDateTime($one, 1970, 1, 10, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 3, 0, 0, 0, 0, 'UTC');
    }

    public function testInvalidDays(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn() => $dateTime->with(day: 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn() => $dateTime->with(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn() => $dateTime->with(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn() => $dateTime->with(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn() => $dateTime->with(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn() => $dateTime->with(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn() => $dateTime->with(2000, 2, 30)
        );
    }

    public function testHour(): void
    {
        $one = DateTime::of(hour: 10);
        $two = $one->with(hour: 3);
        self::assertDateTime($one, 1970, 1, 1, 10, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 3, 0, 0, 0, 'UTC');
    }

    public function testInvalidHours(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn() => $dateTime->with(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn() => $dateTime->with(1970, 1, 1, 24)
        );
    }

    public function testMinute(): void
    {
        $one = DateTime::of(minute: 10);
        $two = $one->with(minute: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 10, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 3, 0, 0, 'UTC');
    }

    public function testInvalidMinutes(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -1 given'),
            fn() => $dateTime->with(minute: -1)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 60 given'),
            fn() => $dateTime->with(1970, 1, 1, 0, 60)
        );
    }

    public function testSecond(): void
    {
        $one = DateTime::of(second: 10);
        $two = $one->with(second: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 10, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 3, 0, 'UTC');
    }

    public function testInvalidSeconds(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn() => $dateTime->with(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 60 given'),
            fn() => $dateTime->with(1970, 1, 1, 0, 0, 60)
        );
    }

    public function testMicrosecond(): void
    {
        $one = DateTime::of(microsecond: 10);
        $two = $one->with(microsecond: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 10, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 3, 'UTC');
    }

    public function testInvalidMicroseconds(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, -1 given'),
            fn() => $dateTime->with(microsecond: -1)
        );
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, 1000000 given'),
            fn() => $dateTime->with(1970, 1, 1, 0, 0, 0, 1_000_000)
        );
    }

    public function testTimeZone(): void
    {
        $one = DateTime::of(timeZone: 'EST');
        $two = $one->with(timeZone: 'MST');
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 0, 'EST');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 0, 'MST');

        $one = DateTime::of(timeZone: TimeZone::of('EST'));
        $two = $one->with(timeZone: TimeZone::of('MST'));
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 0, 'EST');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 0, 'MST');

        $one = DateTime::of(timeZone: Offset::of(4, 30));
        $two = $one->with(timeZone: Offset::of(-3));
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 0, '+04:30');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 0, '-03:00');
    }

    public function testInvalidTimeZones(): void
    {
        $dateTime = DateTime::epoch();

        self::assertException(
            new TimeZoneException('Mars/Phobos'),
            fn() => $dateTime->with(timeZone: 'Mars/Phobos')
        );
        self::assertException(
            new TimeZoneException('CET+04:45'),
            fn() => $dateTime->with(1970, 1, 1, 0, 0, 0, 0, 'CET+04:45')
        );
    }

    public function testAll(): void
    {
        $one = DateTime::parse('0010-10-10 10:10:10.000010 EST', 'Y-m-d H:i:s.u e')->orFail();
        $two = $one->with(3, 3, 3, 3, 3, 3, 3, 'MST');
        self::assertDateTime($one, 10, 10, 10, 10, 10, 10, 10, 'EST');
        self::assertDateTime($two, 3, 3, 3, 3, 3, 3, 3, 'MST');
    }
}
