<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class CreationTest extends TestCase
{
    public function testDefaults(): void
    {
        $time = LocalTime::of();
        self::assertLocalTime($time, 0, 0, 0, 0);
    }

    public function testHourAndDefaultMinSecToZero(): void
    {
        $time = LocalTime::of(hour: 14);
        self::assertSame(14, $time->hour());
        self::assertSame(0, $time->minute());
        self::assertSame(0, $time->second());
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
            new OutOfRangeException('minute must be between 0 and 59, -2 given'),
            fn () => LocalTime::of(0, -2)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 62 given'),
            fn () => LocalTime::of(0, 62)
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
            new OutOfRangeException('second must be between 0 and 59, 61 given'),
            fn () => LocalTime::of(0, 0, 61)
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
}
