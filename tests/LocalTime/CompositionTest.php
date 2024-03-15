<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;
use OutOfRangeException;

final class CompositionTest extends TestCase
{
    public function testDate(): void
    {
        $time = LocalTime::of(4, 5, 6, 7);

        $dateTime1 = $time->atDate(LocalDate::of(1, 2, 3));
        self::assertLocalDateTime($dateTime1, 1, 2, 3, 4, 5, 6, 7);

        $dateTime2 = $time->atDate(1, 2, 3);
        self::assertLocalDateTime($dateTime2, 1, 2, 3, 4, 5, 6, 7);
    }

    public function testInvalidMonths(): void
    {
        $time = LocalTime::midnight();

        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => $time->atDate(month: 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => $time->atDate(1986, 13)
        );
    }

    public function testInvalidDays(): void
    {
        $time = LocalTime::midnight();

        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => $time->atDate(day: 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => $time->atDate(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => $time->atDate(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $time->atDate(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $time->atDate(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $time->atDate(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $time->atDate(2000, 2, 30)
        );
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a local date is passed'),
            fn () => LocalTime::of(0, 0, 0)->atDate(LocalDate::of(year: 1986), month: 9)
        );
    }
}
