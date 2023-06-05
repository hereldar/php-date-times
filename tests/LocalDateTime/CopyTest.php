<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = LocalDateTime::of(year: 10);
        $two = $one->with(year: 3);
        self::assertLocalDateTime($one, 10, 1, 1, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 3, 1, 1, 0, 0, 0, 0);
    }

    public function testMonth(): void
    {
        $one = LocalDateTime::of(month: 10);
        $two = $one->with(month: 3);
        self::assertLocalDateTime($one, 1970, 10, 1, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 3, 1, 0, 0, 0, 0);
    }

    public function testInvalidMonths(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => $dateTime->with(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => $dateTime->with(month: 13)
        );
    }

    public function testDay(): void
    {
        $one = LocalDateTime::of(day: 10);
        $two = $one->with(day: 3);
        self::assertLocalDateTime($one, 1970, 1, 10, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 3, 0, 0, 0, 0);
    }

    public function testInvalidDays(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => $dateTime->with(day: 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => $dateTime->with(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => $dateTime->with(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $dateTime->with(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $dateTime->with(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $dateTime->with(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $dateTime->with(2000, 2, 30)
        );
    }

    public function testHour(): void
    {
        $one = LocalDateTime::of(hour: 10);
        $two = $one->with(hour: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 10, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 3, 0, 0, 0);
    }

    public function testInvalidHours(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn () => $dateTime->with(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn () => $dateTime->with(1970, 1, 1, 24)
        );
    }

    public function testMinute(): void
    {
        $one = LocalDateTime::of(minute: 10);
        $two = $one->with(minute: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 10, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 3, 0, 0);
    }

    public function testInvalidMinutes(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -1 given'),
            fn () => $dateTime->with(minute: -1)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 60 given'),
            fn () => $dateTime->with(1970, 1, 1, 0, 60)
        );
    }

    public function testSecond(): void
    {
        $one = LocalDateTime::of(second: 10);
        $two = $one->with(second: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 0, 10, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 0, 3, 0);
    }

    public function testInvalidSeconds(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn () => $dateTime->with(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 60 given'),
            fn () => $dateTime->with(1970, 1, 1, 0, 0, 60)
        );
    }

    public function testMicrosecond(): void
    {
        $one = LocalDateTime::of(microsecond: 10);
        $two = $one->with(microsecond: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 0, 0, 10);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 0, 0, 3);
    }

    public function testInvalidMicroseconds(): void
    {
        $dateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, -1 given'),
            fn () => $dateTime->with(microsecond: -1)
        );
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, 1000000 given'),
            fn () => $dateTime->with(1970, 1, 1, 0, 0, 0, 1_000_000)
        );
    }

    public function testAll(): void
    {
        $one = LocalDateTime::parse('0010-10-10 10:10:10.000010', 'Y-m-d H:i:s.u')->orFail();
        $two = $one->with(3, 3, 3, 3, 3, 3, 3);
        self::assertLocalDateTime($one, 10, 10, 10, 10, 10, 10, 10);
        self::assertLocalDateTime($two, 3, 3, 3, 3, 3, 3, 3);
    }
}
