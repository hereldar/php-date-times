<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class CreationTest extends TestCase
{
    public function testDefaults(): void
    {
        $dateTime = LocalDate::of();
        self::assertLocalDate($dateTime, 1970, 1, 1);
    }

    public function testYear(): void
    {
        $dateTime = LocalDate::of(1986);
        self::assertSame(1986, $dateTime->year());
    }

    public function testNegativeYear(): void
    {
        $dateTime = LocalDate::of(-1, 10, 12);
        self::assertLocalDate($dateTime, -1, 10, 12);
    }

    public function testManyDigitsPositiveYear(): void
    {
        $dateTime = LocalDate::of(999999999, 10, 12);
        self::assertLocalDate($dateTime, 999999999, 10, 12);
    }

    public function testManyDigitsNegativeYear(): void
    {
        $dateTime = LocalDate::of(-999999999, 10, 12);
        self::assertLocalDate($dateTime, -999999999, 10, 12);
    }

    public function testMonth(): void
    {
        $dateTime = LocalDate::of(month: 3);
        self::assertSame(3, $dateTime->month());
    }

    public function testInvalidMonths(): void
    {
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, -5 given'),
            fn () => LocalDate::of(-2, -5)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => LocalDate::of(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => LocalDate::of(month: 13)
        );
    }

    public function testDay(): void
    {
        $dateTime = LocalDate::of(day: 21);
        self::assertSame(21, $dateTime->day());
    }

    public function testInvalidDays(): void
    {
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, -2 given'),
            fn () => LocalDate::of(day: -2)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => LocalDate::of(-1, 1, 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => LocalDate::of(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => LocalDate::of(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => LocalDate::of(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => LocalDate::of(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => LocalDate::of(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => LocalDate::of(2000, 2, 30)
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
            (new NativeDateTime('today', $tz))->format('Y-n-j 0:00:00.0'),
            new NativeTimeZone('UTC')
        );
        self::assertNotFalse($a);
        $b = LocalDate::today($timeZoneName)->toNative();
        $diff = $a->diff($b);
        self::assertSame('UTC', $b->getTimezone()->getName());
        self::assertSame(0, $diff->days);
        self::assertSame(0, $diff->h);
        self::assertSame(0, $diff->m);
        self::assertSame(0, $diff->s);
        self::assertLessThan(0.1, $diff->f);
    }
}
