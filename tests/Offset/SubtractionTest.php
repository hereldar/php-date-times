<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use ArithmeticError;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class SubtractionTest extends TestCase
{
    public function testMinusUnits(): void
    {
        $offset = Offset::of(hours: 3)->minus(hours: 4);
        self::assertOffset($offset, -1);

        $offset = Offset::of(minutes: 3)->minus(minutes: 4);
        self::assertOffset($offset, 0, -1);

        $offset = Offset::of(seconds: 3)->minus(seconds: 4);
        self::assertOffset($offset, 0, 0, -1);

        $offset = Offset::of(8, 10, 12)->minus(2, 3, 4);
        self::assertOffset($offset, 6, 7, 8);
    }

    public function testMinusNegativeUnits(): void
    {
        $offset = Offset::of(hours: 3)->minus(hours: -4);
        self::assertOffset($offset, 7);

        $offset = Offset::of(minutes: 3)->minus(minutes: -4);
        self::assertOffset($offset, 0, 7);

        $offset = Offset::of(seconds: 3)->minus(seconds: -4);
        self::assertOffset($offset, 0, 0, 7);

        $offset = Offset::of(8, 10, 12)->minus(-2, -3, -4);
        self::assertOffset($offset, 10, 13, 16);
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn () => Offset::zero()->plus(minutes: \PHP_INT_MIN)
        );
        self::assertException(
            ArithmeticError::class,
            fn () => Offset::zero()->plus(seconds: \PHP_INT_MIN, hours: -1)
        );
    }

    public function testOutOfRangeException(): void
    {
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MIN)->minus(hours: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MIN, Offset::MINUTES_MIN)->minus(minutes: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(Offset::HOURS_MIN, Offset::MINUTES_MIN, Offset::SECONDS_MIN)->minus(seconds: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::fromTotalMinutes(Offset::TOTAL_MINUTES_MIN)->minus(minutes: 1)
        );
        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::fromTotalSeconds(Offset::TOTAL_SECONDS_MIN)->minus(seconds: 1)
        );
    }
}
