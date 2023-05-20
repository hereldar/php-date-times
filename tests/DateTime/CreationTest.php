<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;
use OutOfRangeException;

final class CreationTest extends TestCase
{
    public function testDefaults(): void
    {
        $dateTime = DateTime::of();
        self::assertDateTime($dateTime, 1970, 1, 1, 0, 0, 0, 0, 'UTC');
    }

    public function testYear(): void
    {
        $dateTime = DateTime::of(1986);
        self::assertSame(1986, $dateTime->year());
    }

    public function testNegativeYear(): void
    {
        $dateTime = DateTime::of(-1, 10, 12, 1, 2, 3);
        self::assertDateTime($dateTime, -1, 10, 12, 1, 2, 3);
    }

    public function testManyDigitsPositiveYear(): void
    {
        $dateTime = DateTime::of(999999999, 10, 12, 1, 2, 3);
        self::assertDateTime($dateTime, 999999999, 10, 12, 1, 2, 3);
    }

    public function testManyDigitsNegativeYear(): void
    {
        $dateTime = DateTime::of(-999999999, 10, 12, 1, 2, 3);
        self::assertDateTime($dateTime, -999999999, 10, 12, 1, 2, 3);
    }

    public function testMonth(): void
    {
        $dateTime = DateTime::of(month: 3);
        self::assertSame(3, $dateTime->month());
    }

    public function testInvalidMonths(): void
    {
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, -5 given'),
            fn () => DateTime::of(-2, -5)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => DateTime::of(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => DateTime::of(month: 13)
        );
    }

    public function testDay(): void
    {
        $dateTime = DateTime::of(day: 21);
        self::assertSame(21, $dateTime->day());
    }

    public function testInvalidDays(): void
    {
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, -2 given'),
            fn () => DateTime::of(day: -2)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => DateTime::of(-1, 1, 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => DateTime::of(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => DateTime::of(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => DateTime::of(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => DateTime::of(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => DateTime::of(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => DateTime::of(2000, 2, 30)
        );
    }

    public function testHourAndDefaultMinSecToZero(): void
    {
        $dateTime = DateTime::of(hour:  14);
        self::assertSame(14, $dateTime->hour());
        self::assertSame(0, $dateTime->minute());
        self::assertSame(0, $dateTime->second());
    }

    public function testInvalidHours(): void
    {
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn () => DateTime::of(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn () => DateTime::of(1986, 1, 1, 24)
        );
    }

    public function testMinute(): void
    {
        $dateTime = DateTime::of(minute: 58);
        self::assertSame(58, $dateTime->minute());
    }

    public function testInvalidMinutes(): void
    {
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -2 given'),
            fn () => DateTime::of(1986, 1, 1, 0, -2)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 62 given'),
            fn () => DateTime::of(1986, 1, 1, 0, 62)
        );
    }

    public function testSecond(): void
    {
        $dateTime = DateTime::of(second: 59);
        self::assertSame(59, $dateTime->second());
    }

    public function testInvalidSeconds(): void
    {
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn () => DateTime::of(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 61 given'),
            fn () => DateTime::of(1986, 1, 1, 0, 0, 61)
        );
    }

    public function testTimeZone(): void
    {
        $dateTime = DateTime::of(1986, 1, 1, 0, 0, 0, 0, TimeZone::of('Europe/London'));
        self::assertDateTime($dateTime, 1986, 1, 1, 0, 0, 0);
        self::assertSame('Europe/London', $dateTime->timeZone()->name());
    }

    public function testOffset(): void
    {
        $dateTime = DateTime::of(1986, 1, 1, 0, 0, 0, 0, Offset::of(2));
        self::assertDateTime($dateTime, 1986, 1, 1, 0, 0, 0);
        self::assertSame('+02:00', $dateTime->timeZone()->name());
    }

    public function testTimeZoneName(): void
    {
        $dateTime = DateTime::of(1986, 1, 1, 0, 0, 0, 0, 'Europe/London');
        self::assertDateTime($dateTime, 1986, 1, 1, 0, 0, 0);
        self::assertSame('Europe/London', $dateTime->timeZone()->name());
    }

    /**
     * @dataProvider timeZoneNames
     */
    public function testNow(string $timeZoneName): void
    {
        $tz = new NativeTimeZone($timeZoneName);
        $a = new NativeDateTime('now', $tz);
        $b = DateTime::now($timeZoneName)->toNative();
        $diff = $a->diff($b);
        self::assertSame($tz->getName(), $b->getTimezone()->getName());
        self::assertSame(0, $diff->days);
        self::assertSame(0, $diff->h);
        self::assertSame(0, $diff->m);
        self::assertSame(0, $diff->s);
        self::assertLessThan(0.1, $diff->f);
    }
}
