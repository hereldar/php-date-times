<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

/**
 * @internal
 */
final class CustomLocalDate extends LocalDate
{
}

final class CreationTest extends TestCase
{
    public function testDayOfMonthDefaults(): void
    {
        $date = LocalDate::of();
        self::assertLocalDate($date, 1970, 1, 1);
    }

    public function testDayOfMonthYear(): void
    {
        $date = LocalDate::of(1986);
        self::assertSame(1986, $date->year());
    }

    public function testDayOfMonthNegativeYear(): void
    {
        $date = LocalDate::of(-1, 10, 12);
        self::assertLocalDate($date, -1, 10, 12);
    }

    public function testDayOfMonthManyDigitsPositiveYear(): void
    {
        $date = LocalDate::of(999999999, 10, 12);
        self::assertLocalDate($date, 999999999, 10, 12);
    }

    public function testDayOfMonthManyDigitsNegativeYear(): void
    {
        $date = LocalDate::of(-999999999, 10, 12);
        self::assertLocalDate($date, -999999999, 10, 12);
    }

    public function testDayOfMonthMonth(): void
    {
        $date = LocalDate::of(month: 3);
        self::assertSame(3, $date->month());
    }

    public function testDayOfMonthInvalidMonths(): void
    {
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, -1 given'),
            fn () => LocalDate::of(-2, -1)
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

    public function testDayOfMonthDay(): void
    {
        $date = LocalDate::of(day: 21);
        self::assertSame(21, $date->day());
    }

    public function testDayOfMonthInvalidDays(): void
    {
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, -1 given'),
            fn () => LocalDate::of(day: -1)
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

    public function testDayOfYearDefaults(): void
    {
        $date = LocalDate::fromDayOfYear();
        self::assertLocalDate($date, 1970, 1, 1);
    }

    public function testDayOfYearYear(): void
    {
        $date = LocalDate::fromDayOfYear(1986);
        self::assertSame(1986, $date->year());
    }

    public function testDayOfYearNegativeYear(): void
    {
        $date = LocalDate::fromDayOfYear(-1, 40);
        self::assertLocalDate($date, -1, 2, 9);
    }

    public function testDayOfYearManyDigitsPositiveYear(): void
    {
        $date = LocalDate::fromDayOfYear(999999999, 40);
        self::assertLocalDate($date, 999999999, 2, 9);
    }

    public function testDayOfYearManyDigitsNegativeYear(): void
    {
        $date = LocalDate::fromDayOfYear(-999999999, 40);
        self::assertLocalDate($date, -999999999, 2, 9);
    }

    public function testDayOfYearDay(): void
    {
        $date = LocalDate::fromDayOfYear(day: 50);
        self::assertSame(2, $date->month());
        self::assertSame(19, $date->day());
    }

    public function testDayOfYearInvalidDays(): void
    {
        self::assertException(
            new OutOfRangeException('day must be between 1 and 365, -1 given'),
            fn () => LocalDate::fromDayOfYear(day: -1)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 365, 0 given'),
            fn () => LocalDate::fromDayOfYear(-1, 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 365, 366 given'),
            fn () => LocalDate::fromDayOfYear(1986, 366)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 366, 367 given'),
            fn () => LocalDate::fromDayOfYear(1960, 367)
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
        $b = LocalDate::now($timeZoneName)->toNative();
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
        $date = LocalDate::epoch();
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertLocalDate($date, 1970, 1, 1);

        $date = CustomLocalDate::epoch();
        self::assertInstanceOf(CustomLocalDate::class, $date);
        self::assertLocalDate($date, 1970, 1, 1);
    }
}
