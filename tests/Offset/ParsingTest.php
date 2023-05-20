<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;
use Throwable;

final class ParsingTest extends TestCase
{
    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            Offset::parse('', '%H:%i:%s')->orFail();
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
            Offset::parse('01:30:25', '%H:%i')->orFail();
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
            Offset::parse('4', '%N')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('%N', $e->format());
        }
    }

    public function testHours(): void
    {
        $offset = Offset::parse('1', '%h')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 1, 0, 0);


        $offset = Offset::parse('15', '%H')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 15, 0, 0);

        $this->expectException(OutOfRangeException::class);

        /** @psalm-suppress UnusedMethodCall */
        Offset::parse('16', '%H')->orFail();
    }

    public function testMinutes(): void
    {
        $offset = Offset::parse('1', '%i')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 0, 1, 0);

        $offset = Offset::parse('59', '%I')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 0, 59, 0);

        $this->expectException(OutOfRangeException::class);

        /** @psalm-suppress UnusedMethodCall */
        Offset::parse('60', '%I')->orFail();
    }

    public function testSeconds(): void
    {
        $offset = Offset::parse('1', '%s')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 0, 0, 1);

        $offset = Offset::parse('59', '%S')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 0, 0, 59);

        $this->expectException(OutOfRangeException::class);

        /** @psalm-suppress UnusedMethodCall */
        Offset::parse('60', '%S')->orFail();
    }

    public function testAll(): void
    {
        $offset = Offset::parse('01:02:03', '%H:%I:%S')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 1, 2, 3);
    }

    public function testCopy(): void
    {
        $one = Offset::parse('10:10:10', '%h:%i:%s')->orFail();
        $two = $one->with(hours: 3, minutes: 3, seconds: 3);
        self::assertOffset($one, 10, 10, 10);
        self::assertOffset($two, 3, 3, 3);
    }
}
