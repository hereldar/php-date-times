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
        $date = LocalDate::of(1, 2, 3);

        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, -1 given'),
            fn () => $date->atTime(hour: -1)
        );
        self::assertException(
            new OutOfRangeException('hour must be between 0 and 23, 24 given'),
            fn () => $date->atTime(24)
        );
    }

    public function testInvalidMinutes(): void
    {
        $date = LocalDate::of(1, 2, 3);

        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, -2 given'),
            fn () => $date->atTime(0, -2)
        );
        self::assertException(
            new OutOfRangeException('minute must be between 0 and 59, 62 given'),
            fn () => $date->atTime(0, 62)
        );
    }

    public function testInvalidSeconds(): void
    {
        $date = LocalDate::of(1, 2, 3);

        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, -1 given'),
            fn () => $date->atTime(second: -1)
        );
        self::assertException(
            new OutOfRangeException('second must be between 0 and 59, 61 given'),
            fn () => $date->atTime(0, 0, 61)
        );
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a local time is passed'),
            fn () => LocalDate::of(1986, 9, 25)->atTime(LocalTime::of(hour: 1), minute: 2)
        );
    }
}
