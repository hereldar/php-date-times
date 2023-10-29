<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use TypeError;

/** @internal */
final class CustomPeriod extends Period {}

final class CreationTest extends TestCase
{
    public function testYears(): void
    {
        $period = Period::of(years: 1);
        self::assertPeriod($period, 1, 0, 0, 0, 0, 0, 0);
    }

    public function testMonths(): void
    {
        $period = Period::of(months: 2);
        self::assertPeriod($period, 0, 2, 0, 0, 0, 0, 0);
    }

    public function testDays(): void
    {
        $period = Period::of(days: 3);
        self::assertPeriod($period, 0, 0, 3, 0, 0, 0, 0);
    }

    public function testHours(): void
    {
        $period = Period::of(hours: 4);
        self::assertPeriod($period, 0, 0, 0, 4, 0, 0, 0);
    }

    public function testMinutes(): void
    {
        $period = Period::of(minutes: 5);
        self::assertPeriod($period, 0, 0, 0, 0, 5, 0, 0);
    }

    public function testSeconds(): void
    {
        $period = Period::of(seconds: 6);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 6, 0);
    }

    public function testMicroseconds(): void
    {
        $period = Period::of(microseconds: 7);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 7);
    }

    public function testMillennia(): void
    {
        $period = Period::of(millennia: 1);
        self::assertPeriod($period, 1 * 1_000, 0, 0, 0, 0, 0, 0);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 1000 is not an integer'),
            fn() => Period::of(millennia: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 1000 is not an integer'),
            fn() => Period::of(millennia: PHP_INT_MIN)
        );
    }

    public function testCenturies(): void
    {
        $period = Period::of(centuries: 2);
        self::assertPeriod($period, 2 * 100, 0, 0, 0, 0, 0, 0);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 100 is not an integer'),
            fn() => Period::of(centuries: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 100 is not an integer'),
            fn() => Period::of(centuries: PHP_INT_MIN)
        );
    }

    public function testDecades(): void
    {
        $period = Period::of(decades: 3);
        self::assertPeriod($period, 3 * 10, 0, 0, 0, 0, 0, 0);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 10 is not an integer'),
            fn() => Period::of(decades: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 10 is not an integer'),
            fn() => Period::of(decades: PHP_INT_MIN)
        );
    }

    public function testQuarters(): void
    {
        $period = Period::of(quarters: 4);
        self::assertPeriod($period, 0, 4 * 3, 0, 0, 0, 0, 0);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 3 is not an integer'),
            fn() => Period::of(quarters: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 3 is not an integer'),
            fn() => Period::of(quarters: PHP_INT_MIN)
        );
    }

    public function testWeeks(): void
    {
        $period = Period::of(weeks: 5);
        self::assertPeriod($period, 0, 0, 5 * 7, 0, 0, 0, 0);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 7 is not an integer'),
            fn() => Period::of(weeks: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 7 is not an integer'),
            fn() => Period::of(weeks: PHP_INT_MIN)
        );
    }

    public function testMilliseconds(): void
    {
        $period = Period::of(milliseconds: 6);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 6 * 1_000);

        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 1000 is not an integer'),
            fn() => Period::of(milliseconds: PHP_INT_MAX)
        );
        self::assertException(
            new ArithmeticError('Multiplication of -9223372036854775808 by 1000 is not an integer'),
            fn() => Period::of(milliseconds: PHP_INT_MIN)
        );
    }

    public function testAll(): void
    {
        $period = Period::of(1, 2, 3, 4, 5, 6, 7);
        self::assertPeriod($period, 1, 2, 3, 4, 5, 6, 7);

        self::assertException(
            new ArithmeticError('Addition of 9223372036854775807 plus 0, 1000 and 0 is not an integer'),
            fn() => Period::of(years: PHP_INT_MAX, centuries: 10)
        );
    }

    public function testZero(): void
    {
        $period = Period::zero();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 0);

        $period = CustomPeriod::zero();
        self::assertInstanceOf(CustomPeriod::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 0);
    }

    public function testBetweenDateTimes(): void
    {
        $period = CustomPeriod::between(
            DateTime::fromIso8601('2010-01-15T03:00:15Z'),
            DateTime::fromIso8601('2011-03-18T15:45:45.723+02:00', milliseconds: true),
        );
        self::assertInstanceOf(CustomPeriod::class, $period);
        self::assertPeriod($period, 1, 2, 3, 10, 45, 30, 723_000);

        self::assertTrue(Period::between(
            DateTime::epoch(),
            DateTime::epoch(),
        )->isZero());

        self::assertException(
            TypeError::class,
            fn() => Period::between(DateTime::epoch(), LocalDateTime::epoch())
        );
    }

    public function testBetweenLocalDateTimes(): void
    {
        $period = CustomPeriod::between(
            LocalDateTime::fromIso8601('2010-01-15T03:00:15'),
            LocalDateTime::fromIso8601('2011-03-18T15:45:45.723', milliseconds: true),
        );
        self::assertInstanceOf(CustomPeriod::class, $period);
        self::assertPeriod($period, 1, 2, 3, 12, 45, 30, 723_000);

        self::assertTrue(Period::between(
            LocalDateTime::epoch(),
            LocalDateTime::epoch(),
        )->isZero());

        self::assertException(
            TypeError::class,
            fn() => Period::between(LocalDateTime::epoch(), DateTime::epoch())
        );
    }

    public function testBetweenLocalDates(): void
    {
        $period = CustomPeriod::between(
            LocalDate::fromIso8601('2010-01-15'),
            LocalDate::fromIso8601('2011-03-18'),
        );
        self::assertInstanceOf(CustomPeriod::class, $period);
        self::assertPeriod($period, 1, 2, 3, 0, 0, 0, 0);

        self::assertTrue(Period::between(
            LocalDate::epoch(),
            LocalDate::epoch(),
        )->isZero());

        self::assertException(
            TypeError::class,
            fn() => Period::between(LocalDate::epoch(), DateTime::epoch())
        );
    }

    public function testBetweenLocalTimes(): void
    {
        $period = CustomPeriod::between(
            LocalTime::fromIso8601('03:00:15'),
            LocalTime::fromIso8601('15:45:45.723', milliseconds: true),
        );
        self::assertInstanceOf(CustomPeriod::class, $period);
        self::assertPeriod($period, 0, 0, 0, 12, 45, 30, 723_000);

        self::assertTrue(Period::between(
            LocalTime::epoch(),
            LocalTime::epoch(),
        )->isZero());

        self::assertException(
            TypeError::class,
            fn() => Period::between(LocalTime::epoch(), DateTime::epoch())
        );
    }
}
