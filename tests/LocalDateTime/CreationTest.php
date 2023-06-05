<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

/**
 * @internal
 */
final class CustomLocalDateTime extends LocalDateTime
{
}

final class CreationTest extends TestCase
{
    public function testDefaults(): void
    {
        $dateTime = LocalDateTime::of();
        self::assertLocalDateTime($dateTime, 1970, 1, 1, 0, 0, 0, 0);
    }

    public function testYear(): void
    {
        $dateTime = LocalDateTime::of(1986);
        self::assertSame(1986, $dateTime->year());
    }

    public function testNegativeYear(): void
    {
        $dateTime = LocalDateTime::of(-1, 10, 12, 1, 2, 3);
        self::assertLocalDateTime($dateTime, -1, 10, 12, 1, 2, 3);
    }

    public function testManyDigitsPositiveYear(): void
    {
        $dateTime = LocalDateTime::of(999999999, 10, 12, 1, 2, 3);
        self::assertLocalDateTime($dateTime, 999999999, 10, 12, 1, 2, 3);
    }

    public function testManyDigitsNegativeYear(): void
    {
        $dateTime = LocalDateTime::of(-999999999, 10, 12, 1, 2, 3);
        self::assertLocalDateTime($dateTime, -999999999, 10, 12, 1, 2, 3);
    }

    public function testMonth(): void
    {
        $dateTime = LocalDateTime::of(month: 3);
        self::assertSame(3, $dateTime->month());
    }

    public function testInvalidMonths(): void
    {
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, -1 given'),
            fn () => LocalDateTime::of(-2, -1)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => LocalDateTime::of(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => LocalDateTime::of(month: 13)
        );
    }

    public function testDay(): void
    {
        $dateTime = LocalDateTime::of(day: 21);
        self::assertSame(21, $dateTime->day());
    }

    public function testInvalidDays(): void
    {
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, -1 given'),
            fn () => LocalDateTime::of(day: -1)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => LocalDateTime::of(-1, 1, 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => LocalDateTime::of(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => LocalDateTime::of(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => LocalDateTime::of(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => LocalDateTime::of(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => LocalDateTime::of(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => LocalDateTime::of(2000, 2, 30)
        );
    }

    public function testHour(): void
    {
        $dateTime = LocalDateTime::of(hour:  14);
        self::assertSame(14, $dateTime->hour());
    }

    public function testInvalidHours(): void
    {
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn () => LocalDateTime::of(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn () => LocalDateTime::of(1986, 1, 1, 24)
        );
    }

    public function testMinute(): void
    {
        $dateTime = LocalDateTime::of(minute: 58);
        self::assertSame(58, $dateTime->minute());
    }

    public function testInvalidMinutes(): void
    {
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -1 given'),
            fn () => LocalDateTime::of(1986, 1, 1, 0, -1)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 60 given'),
            fn () => LocalDateTime::of(1986, 1, 1, 0, 60)
        );
    }

    public function testSecond(): void
    {
        $dateTime = LocalDateTime::of(second: 59);
        self::assertSame(59, $dateTime->second());
    }

    public function testInvalidSeconds(): void
    {
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn () => LocalDateTime::of(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 60 given'),
            fn () => LocalDateTime::of(1986, 1, 1, 0, 0, 60)
        );
    }

    public function testMicrosecond(): void
    {
        $dateTime = LocalDateTime::of(microsecond: 999_999);
        self::assertSame(999_999, $dateTime->microsecond());
    }

    public function testInvalidMicroseconds(): void
    {
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, -1 given'),
            fn () => LocalDateTime::of(microsecond: -1)
        );
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, 1000000 given'),
            fn () => LocalDateTime::of(1986, 1, 1, 0, 0, 0, 1_000_000)
        );
    }

    /**
     * @dataProvider timeZoneNames
     */
    public function testNow(string $timeZoneName): void
    {
        $tz = new NativeTimeZone($timeZoneName);
        $a = NativeDateTime::createFromFormat(
            'Y-n-j G:i:s.u',
            (new NativeDateTime('now', $tz))->format('Y-n-j G:i:s.u'),
            new NativeTimeZone('UTC')
        );
        self::assertNotFalse($a);
        $b = LocalDateTime::now($timeZoneName)->toNative();
        $diff = $a->diff($b);
        self::assertSame('UTC', $b->getTimezone()->getName());
        self::assertSame(0, $diff->days);
        self::assertSame(0, $diff->h);
        self::assertSame(0, $diff->m);
        self::assertSame(0, $diff->s);
        self::assertLessThan(0.1, $diff->f);
    }

    public function testEpoch(): void
    {
        $dateTime = LocalDateTime::epoch();
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertLocalDateTime($dateTime, 1970, 1, 1, 0, 0, 0, 0);

        $dateTime = CustomLocalDateTime::epoch();
        self::assertInstanceOf(CustomLocalDateTime::class, $dateTime);
        self::assertLocalDateTime($dateTime, 1970, 1, 1, 0, 0, 0, 0);
    }
}
