<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use OutOfRangeException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class AdditionTest extends TestCase
{
    public function testPlusUnits(): void
    {
        $offset = Offset::of(hours: 3)->plus(hours: 4);
        self::assertOffset($offset, 7);

        $offset = Offset::of(minutes: 3)->plus(minutes: 4);
        self::assertOffset($offset, 0, 7);

        $offset = Offset::of(seconds: 3)->plus(seconds: 4);
        self::assertOffset($offset, 0, 0, 7);

        $offset = Offset::of(8, 10, 12)->plus(2, 3, 4);
        self::assertOffset($offset, 10, 13, 16);
    }

    public function testPlusNegativeUnits(): void
    {
        $offset = Offset::of(hours: 3)->plus(hours: -4);
        self::assertOffset($offset, -1);

        $offset = Offset::of(minutes: 3)->plus(minutes: -4);
        self::assertOffset($offset, 0, -1);

        $offset = Offset::of(seconds: 3)->plus(seconds: -4);
        self::assertOffset($offset, 0, 0, -1);

        $offset = Offset::of(8, 10, 12)->plus(-2, -3, -4);
        self::assertOffset($offset, 6, 7, 8);
    }

    public function testOutOfRangeException(): void
    {
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MAX)->plus(hours: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MAX, Offset::MINUTES_MAX)->plus(minutes: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MAX, Offset::MINUTES_MAX, Offset::SECONDS_MAX)->plus(seconds: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::fromTotalMinutes(Offset::TOTAL_MINUTES_MAX)->plus(minutes: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::fromTotalSeconds(Offset::TOTAL_SECONDS_MAX)->plus(seconds: 1)
        );
    }
}
