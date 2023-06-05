<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = LocalDate::of(year: 10);
        $two = $one->with(year: 3);
        self::assertLocalDate($one, 10, 1, 1);
        self::assertLocalDate($two, 3, 1, 1);
    }

    public function testMonth(): void
    {
        $one = LocalDate::of(month: 10);
        $two = $one->with(month: 3);
        self::assertLocalDate($one, 1970, 10, 1);
        self::assertLocalDate($two, 1970, 3, 1);
    }

    public function testInvalidMonths(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 0 given'),
            fn () => $date->with(1986, 0)
        );
        self::assertException(
            new OutOfRangeException('month must be between 1 and 12, 13 given'),
            fn () => $date->with(month: 13)
        );
    }

    public function testDay(): void
    {
        $one = LocalDate::of(day: 10);
        $two = $one->with(day: 3);
        self::assertLocalDate($one, 1970, 1, 10);
        self::assertLocalDate($two, 1970, 1, 3);
    }

    public function testInvalidDays(): void
    {
        $date = LocalDate::epoch();

        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 0 given'),
            fn () => $date->with(day: 0)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 31, 32 given'),
            fn () => $date->with(1986, 1, 32)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 30, 31 given'),
            fn () => $date->with(1986, 4, 31)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $date->with(1986, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $date->with(1960, 2, 30)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 28, 29 given'),
            fn () => $date->with(1900, 2, 29)
        );
        self::assertException(
            new OutOfRangeException('day must be between 1 and 29, 30 given'),
            fn () => $date->with(2000, 2, 30)
        );
    }

    public function testAll(): void
    {
        $one = LocalDate::parse('0010-10-10', 'Y-m-d')->orFail();
        $two = $one->with(3, 3, 3);
        self::assertLocalDate($one, 10, 10, 10);
        self::assertLocalDate($two, 3, 3, 3);
    }
}
