<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use ArithmeticError;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;
use Throwable;

final class ParsingTest extends TestCase
{
    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            Period::parse('', '%H:%i:%s')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('', $e->string());
            self::assertSame('%H:%i:%s', $e->format());
        }
    }

    public function testTrailingData(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            Period::parse('01:30:25', '%H:%i')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('01:30:25', $e->string());
            self::assertSame('%H:%i', $e->format());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            Period::parse('4', '%N')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('%N', $e->format());
        }
    }

    public function testYears(): void
    {
        $period = Period::parse('1', '%y')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 1, 0, 0, 0, 0, 0);

        $period = Period::parse('2000', '%Y')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 2000, 0, 0, 0, 0, 0);
    }

    public function testMonths(): void
    {
        $period = Period::parse('1', '%m')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 1, 0, 0, 0, 0);

        $period = Period::parse('20', '%M')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 20, 0, 0, 0, 0);
    }

    public function testWeeks(): void
    {
        $period = Period::parse('1', '%w')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 7, 0, 0, 0);

        $period = Period::parse('20', '%W')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 140, 0, 0, 0);
    }

    public function testDays(): void
    {
        $period = Period::parse('1', '%d')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 1, 0, 0, 0);

        $period = Period::parse('20', '%D')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 20, 0, 0, 0);
    }

    public function testWeeksAndDays(): void
    {
        $period = Period::parse('3 5', '%w %e')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 26, 0, 0, 0);

        $period = Period::parse('02 -06', '%W %E')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 8, 0, 0, 0);
    }

    public function testHours(): void
    {
        $period = Period::parse('1', '%h')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 1, 0, 0);

        $period = Period::parse('20', '%H')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 20, 0, 0);
    }

    public function testMinutes(): void
    {
        $period = Period::parse('1', '%i')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 1, 0);

        $period = Period::parse('20', '%I')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 20, 0);
    }

    public function testSeconds(): void
    {
        $period = Period::parse('1', '%s')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 1);

        $period = Period::parse('20', '%S')->orFail();
        self::assertPeriod($period, 0, 0, 0, 0, 0, 20);
    }

    public function testDecimalSeconds(): void
    {
        $period = Period::parse('1.5', '%s%f')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 1, 500000);

        $period = Period::parse('01.253400', '%S%F')->orFail();
        self::assertPeriod($period, 0, 0, 0, 0, 0, 1, 253400);
    }

    public function testMilliseconds(): void
    {
        $period = Period::parse('1', '%v')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 1000);
        self::assertSame(1000, $period->microseconds());

        $period = Period::parse('200', '%V')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 200000);
    }

    public function testMicroseconds(): void
    {
        $period = Period::parse('1', '%u')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 1);
        self::assertSame(1, $period->microseconds());

        $period = Period::parse('200000', '%u')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 0, 0, 0, 0, 0, 0, 200000);
    }

    public function testAll(): void
    {
        $period = Period::parse('2000-01-02 03:04:05.500000', '%Y-%M-%D %H:%I:%S.%U')->orFail();
        self::assertInstanceOf(Period::class, $period);
        self::assertPeriod($period, 2000, 1, 2, 3, 4, 5, 500000);
    }

    public function testMultipleFormats(): void
    {
        self::assertException(
            InvalidArgumentException::class,
            fn () => Period::parse('01:02:03', [])
        );
        self::assertEquals(
            Period::of(1, 2, 3),
            Period::parse('0001/02/03', ['%Y/%M/%D'])->orFail()
        );
        self::assertEquals(
            Period::of(1, 2, 3),
            Period::parse('0001/02/03', ['%Y%M%D', '%Y/%M/%D'])->orFail()
        );
    }

    public function testArithmeticError(): void
    {
        self::assertException(
            new ArithmeticError('Multiplication of 9223372036854775807 by 7 is not an integer'),
            fn () => Period::parse('9223372036854775807', '%w')->orFail()
        );
        self::assertException(
            new ArithmeticError('Addition of 9223372036854775807 plus 7 is not an integer'),
            fn () => Period::parse('9223372036854775807/1', '%d/%w')->orFail()
        );
    }
}
