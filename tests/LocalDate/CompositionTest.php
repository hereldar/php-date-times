<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;
use OutOfRangeException;

final class CompositionTest extends TestCase
{
    public function testTime(): void
    {
        $date = LocalDate::of(1, 2, 3);

        $dateTime1 = $date->atTime(LocalTime::of(4, 5, 6, 7));
        self::assertLocalDateTime($dateTime1, 1, 2, 3, 4, 5, 6, 7);

        $dateTime2 = $date->atTime(4, 5, 6, 7);
        self::assertLocalDateTime($dateTime2, 1, 2, 3, 4, 5, 6, 7);
    }

    public function testInvalidHours(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn() => $date->atTime(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn() => $date->atTime(24)
        );
    }

    public function testInvalidMinutes(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -1 given'),
            fn() => $date->atTime(minute: -1)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 60 given'),
            fn() => $date->atTime(0, 60)
        );
    }

    public function testInvalidSeconds(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn() => $date->atTime(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 60 given'),
            fn() => $date->atTime(0, 0, 60)
        );
    }

    public function testInvalidMicroseconds(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, -1 given'),
            fn() => $date->atTime(microsecond: -1)
        );
        self::assertException(
            new OutOfRangeException('microsecond must be between 0 and 999999, 1000000 given'),
            fn() => $date->atTime(0, 0, 0, 1_000_000)
        );
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a local time is passed'),
            fn() => LocalDate::of(1986, 9, 25)->atTime(LocalTime::of(hour: 1), minute: 2)
        );
    }
}
