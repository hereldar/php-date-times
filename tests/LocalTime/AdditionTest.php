<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use ArithmeticError;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class AdditionTest extends TestCase
{
    public function testAddHoursPositive(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(1, $time->plus(hours: 1)->hour());
        self::assertSame(1, $time->plus(Period::of(hours: 1))->hour());
    }

    public function testAddHoursZero(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(0, $time->plus(hours: 0)->hour());
        self::assertSame(0, $time->plus(Period::of(hours: 0))->hour());
    }

    public function testAddHoursNegative(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(23, $time->plus(hours: -1)->hour());
        self::assertSame(23, $time->plus(Period::of(hours: -1))->hour());
    }

    public function testAddMinutesPositive(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(1, $time->plus(minutes: 1)->minute());
        self::assertSame(1, $time->plus(Period::of(minutes: 1))->minute());
    }

    public function testAddMinutesZero(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(0, $time->plus(minutes: 0)->minute());
        self::assertSame(0, $time->plus(Period::of(minutes: 0))->minute());
    }

    public function testAddMinutesNegative(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(59, $time->plus(minutes: -1)->minute());
        self::assertSame(59, $time->plus(Period::of(minutes: -1))->minute());
    }

    public function testAddSecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->plus(seconds: 1)->second());
        self::assertSame(1, $time->plus(Period::of(seconds: 1))->second());
    }

    public function testAddSecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->plus(seconds: 0)->second());
        self::assertSame(0, $time->plus(Period::of(seconds: 0))->second());
    }

    public function testAddSecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(59, $time->plus(seconds: -1)->second());
        self::assertSame(59, $time->plus(Period::of(seconds: -1))->second());
    }

    public function testAddMicrosecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->plus(microseconds: 1)->microsecond());
        self::assertSame(1, $time->plus(Period::of(microseconds: 1))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100001, $time->plus(microseconds: 1)->microsecond());
        self::assertSame(100001, $time->plus(Period::of(microseconds: 1))->microsecond());
    }

    public function testAddMicrosecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->plus(microseconds: 0)->microsecond());
        self::assertSame(0, $time->plus(Period::of(microseconds: 0))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100000, $time->plus(microseconds: 0)->microsecond());
        self::assertSame(100000, $time->plus(Period::of(microseconds: 0))->microsecond());
    }

    public function testAddMicrosecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(999999, $time->plus(microseconds: -1)->microsecond());
        self::assertSame(999999, $time->plus(Period::of(microseconds: -1))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(99999, $time->plus(microseconds: -1)->microsecond());
        self::assertSame(99999, $time->plus(Period::of(microseconds: -1))->microsecond());
    }

    public function testAddMillisecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->plus(milliseconds: 1)->millisecond());
        self::assertSame(1, $time->plus(Period::of(milliseconds: 1))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(101, $time->plus(milliseconds: 1)->millisecond());
        self::assertSame(101, $time->plus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testAddMillisecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->plus(milliseconds: 0)->millisecond());
        self::assertSame(0, $time->plus(Period::of(milliseconds: 0))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100, $time->plus(milliseconds: 0)->millisecond());
        self::assertSame(100, $time->plus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testAddMillisecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(999, $time->plus(milliseconds: -1)->millisecond());
        self::assertSame(999, $time->plus(Period::of(milliseconds: -1))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(99, $time->plus(milliseconds: -1)->millisecond());
        self::assertSame(99, $time->plus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a period is passed'),
            fn() => LocalTime::of(0, 0, 0)->plus(Period::of(hours: 1), minutes: 2)
        );
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn() => LocalTime::epoch()->plus(milliseconds: \PHP_INT_MAX)
        );
        self::assertException(
            ArithmeticError::class,
            fn() => LocalTime::epoch()->plus(microseconds: \PHP_INT_MAX, milliseconds: 1)
        );
    }
}
