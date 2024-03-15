<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

/** @internal */
final class CustomLocalTime extends LocalTime {}

final class CreationTest extends TestCase
{
    public function testDefaults(): void
    {
        $time = LocalTime::of();
        self::assertLocalTime($time, 0, 0, 0, 0);
    }

    public function testHour(): void
    {
        $time = LocalTime::of(14);
        self::assertSame(14, $time->hour());
    }

    public function testInvalidHours(): void
    {
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn () => LocalTime::of(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn () => LocalTime::of(24)
        );
    }

    public function testMinute(): void
    {
        $time = LocalTime::of(minute: 58);
        self::assertSame(58, $time->minute());
    }

    public function testInvalidMinutes(): void
    {
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -1 given'),
            fn () => LocalTime::of(minute: -1)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 60 given'),
            fn () => LocalTime::of(0, 60)
        );
    }

    public function testSecond(): void
    {
        $time = LocalTime::of(second: 59);
        self::assertSame(59, $time->second());
    }

    public function testInvalidSeconds(): void
    {
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn () => LocalTime::of(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 60 given'),
            fn () => LocalTime::of(0, 0, 60)
        );
    }

    public function testMicrosecond(): void
    {
        $time = LocalTime::of(microsecond: 999_999);
        self::assertSame(999_999, $time->microsecond());
    }

    public function testInvalidMicroseconds(): void
    {
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, -1 given'),
            fn () => LocalTime::of(microsecond: -1)
        );
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, 1000000 given'),
            fn () => LocalTime::of(0, 0, 0, 1_000_000)
        );
    }

    /**
     * @dataProvider timeZoneNames
     *
     * @param non-empty-string $timeZoneName
     */
    public function testNow(string $timeZoneName): void
    {
        $tz = new NativeTimeZone($timeZoneName);
        $a = NativeDateTime::createFromFormat(
            'Y-n-j G:i:s.u',
            (new NativeDateTime('now', $tz))->format('1970-1-1 G:i:s.u'),
            new NativeTimeZone('UTC')
        );
        self::assertNotFalse($a);
        $b = LocalTime::now($timeZoneName)->toNative();
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
        $time = LocalTime::epoch();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);

        $time = CustomLocalTime::epoch();
        self::assertInstanceOf(CustomLocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);
    }

    public function testMax(): void
    {
        $time = LocalTime::max();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertLocalTime($time, 23, 59, 59, 999_999);

        $time = CustomLocalTime::max();
        self::assertInstanceOf(CustomLocalTime::class, $time);
        self::assertLocalTime($time, 23, 59, 59, 999_999);
    }

    public function testMin(): void
    {
        $time = LocalTime::min();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);

        $time = CustomLocalTime::min();
        self::assertInstanceOf(CustomLocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);
    }

    public function testMidnight(): void
    {
        $time = LocalTime::midnight();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);

        $time = CustomLocalTime::midnight();
        self::assertInstanceOf(CustomLocalTime::class, $time);
        self::assertLocalTime($time, 0, 0, 0, 0);
    }

    public function testNoon(): void
    {
        $time = LocalTime::noon();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertLocalTime($time, 12, 0, 0, 0);

        $time = CustomLocalTime::noon();
        self::assertInstanceOf(CustomLocalTime::class, $time);
        self::assertLocalTime($time, 12, 0, 0, 0);
    }
}
