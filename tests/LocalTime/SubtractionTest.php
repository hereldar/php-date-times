<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use ArithmeticError;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class SubtractionTest extends TestCase
{
    public function testSubtractHoursPositive(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(23, $time->minus(hours: 1)->hour());
        self::assertSame(23, $time->minus(Period::of(hours: 1))->hour());
    }

    public function testSubtractHoursZero(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(0, $time->minus(hours: 0)->hour());
        self::assertSame(0, $time->minus(Period::of(hours: 0))->hour());
    }

    public function testSubtractHoursNegative(): void
    {
        $time = LocalTime::of(0);
        self::assertSame(1, $time->minus(hours: -1)->hour());
        self::assertSame(1, $time->minus(Period::of(hours: -1))->hour());
    }

    public function testSubtractMinutesPositive(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(59, $time->minus(minutes: 1)->minute());
        self::assertSame(59, $time->minus(Period::of(minutes: 1))->minute());
    }

    public function testSubtractMinutesZero(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(0, $time->minus(minutes: 0)->minute());
        self::assertSame(0, $time->minus(Period::of(minutes: 0))->minute());
    }

    public function testSubtractMinutesNegative(): void
    {
        $time = LocalTime::of(0, 0);
        self::assertSame(1, $time->minus(minutes: -1)->minute());
        self::assertSame(1, $time->minus(Period::of(minutes: -1))->minute());
    }

    public function testSubtractSecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(59, $time->minus(seconds: 1)->second());
        self::assertSame(59, $time->minus(Period::of(seconds: 1))->second());
    }

    public function testSubtractSecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->minus(seconds: 0)->second());
        self::assertSame(0, $time->minus(Period::of(seconds: 0))->second());
    }

    public function testSubtractSecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->minus(seconds: -1)->second());
        self::assertSame(1, $time->minus(Period::of(seconds: -1))->second());
    }

    public function testSubtractMicrosecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(999999, $time->minus(microseconds: 1)->microsecond());
        self::assertSame(999999, $time->minus(Period::of(microseconds: 1))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(99999, $time->minus(microseconds: 1)->microsecond());
        self::assertSame(99999, $time->minus(Period::of(microseconds: 1))->microsecond());
    }

    public function testSubtractMicrosecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->minus(microseconds: 0)->microsecond());
        self::assertSame(0, $time->minus(Period::of(microseconds: 0))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100000, $time->minus(microseconds: 0)->microsecond());
        self::assertSame(100000, $time->minus(Period::of(microseconds: 0))->microsecond());
    }

    public function testSubtractMicrosecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->minus(microseconds: -1)->microsecond());
        self::assertSame(1, $time->minus(Period::of(microseconds: -1))->microsecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100001, $time->minus(microseconds: -1)->microsecond());
        self::assertSame(100001, $time->minus(Period::of(microseconds: -1))->microsecond());
    }

    public function testSubtractMillisecondsPositive(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(999, $time->minus(milliseconds: 1)->millisecond());
        self::assertSame(999, $time->minus(Period::of(milliseconds: 1))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(99, $time->minus(milliseconds: 1)->millisecond());
        self::assertSame(99, $time->minus(Period::of(milliseconds: 1))->millisecond());
    }

    public function testSubtractMillisecondsZero(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(0, $time->minus(milliseconds: 0)->millisecond());
        self::assertSame(0, $time->minus(Period::of(milliseconds: 0))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(100, $time->minus(milliseconds: 0)->millisecond());
        self::assertSame(100, $time->minus(Period::of(milliseconds: 0))->millisecond());
    }

    public function testSubtractMillisecondsNegative(): void
    {
        $time = LocalTime::of(0, 0, 0);
        self::assertSame(1, $time->minus(milliseconds: -1)->millisecond());
        self::assertSame(1, $time->minus(Period::of(milliseconds: -1))->millisecond());
        $time = LocalTime::of(0, 0, 0, 100_000);
        self::assertSame(101, $time->minus(milliseconds: -1)->millisecond());
        self::assertSame(101, $time->minus(Period::of(milliseconds: -1))->millisecond());
    }

    public function testInvalidArgumentException(): void
    {
        self::assertException(
            new InvalidArgumentException('No time units are allowed when a period is passed'),
            fn() => LocalTime::of(0, 0, 0)->minus(Period::of(hours: 1), minutes: 2)
        );
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            ArithmeticError::class,
            fn() => LocalTime::epoch()->minus(milliseconds: PHP_INT_MIN)
        );
        self::assertException(
            ArithmeticError::class,
            fn() => LocalTime::epoch()->minus(microseconds: PHP_INT_MIN, milliseconds: -1)
        );
    }
}
