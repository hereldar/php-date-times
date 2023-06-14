<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use OutOfRangeException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class CreationTest extends TestCase
{
    public function testHours(): void
    {
        $offset = Offset::of(hours: 1);
        self::assertOffset($offset, 1, 0, 0);

        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, 16 given'),
            fn () => Offset::of(hours: Offset::HOURS_MAX + 1)
        );
        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, -16 given'),
            fn () => Offset::of(hours: Offset::HOURS_MIN - 1)
        );
    }

    public function testMinutes(): void
    {
        $offset = Offset::of(minutes: 2);
        self::assertOffset($offset, 0, 2, 0);

        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, 60 given'),
            fn () => Offset::of(minutes: Offset::MINUTES_MAX + 1)
        );
        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, -60 given'),
            fn () => Offset::of(minutes: Offset::MINUTES_MIN - 1)
        );
    }

    public function testSeconds(): void
    {
        $offset = Offset::of(seconds: 3);
        self::assertOffset($offset, 0, 0, 3);

        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, 60 given'),
            fn () => Offset::of(seconds: Offset::SECONDS_MAX + 1)
        );
        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, -60 given'),
            fn () => Offset::of(seconds: Offset::SECONDS_MIN - 1)
        );
    }

    public function testAll(): void
    {
        $offset = Offset::of(1, 2, 3);
        self::assertOffset($offset, 1, 2, 3);
    }

    public function testTotalMinutes(): void
    {
        $offset = Offset::fromTotalMinutes((1*60) + 30);
        self::assertOffset($offset, 1, 30, 0);

        self::assertException(
            new OutOfRangeException('minutes must be between -900 and 900, 901 given'),
            fn () => Offset::fromTotalMinutes(Offset::TOTAL_MINUTES_MAX + 1)
        );
        self::assertException(
            new OutOfRangeException('minutes must be between -900 and 900, -901 given'),
            fn () => Offset::fromTotalMinutes(Offset::TOTAL_MINUTES_MIN - 1)
        );
    }

    public function testTotalSeconds(): void
    {
        $offset = Offset::fromTotalSeconds((1*3600) + (30*60) + 45);
        self::assertOffset($offset, 1, 30, 45);

        self::assertException(
            new OutOfRangeException('seconds must be between -54000 and 54000, 54001 given'),
            fn () => Offset::fromTotalSeconds(Offset::TOTAL_SECONDS_MAX + 1)
        );
        self::assertException(
            new OutOfRangeException('seconds must be between -54000 and 54000, -54001 given'),
            fn () => Offset::fromTotalSeconds(Offset::TOTAL_SECONDS_MIN - 1)
        );
    }
}
