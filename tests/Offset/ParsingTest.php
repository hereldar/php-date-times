<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class ParsingTest extends TestCase
{
    public function testEmptyString(): void
    {
        $this->expectException(ParseException::class);
        Offset::parse('', '%H:%i:%s')->orFail();
    }

    public function testTrailingData(): void
    {
        $this->expectException(ParseException::class);
        Offset::parse('01:30:25', '%H:%i')->orFail();
    }

    public function testInvalidSubstitute(): void
    {
        $this->expectException(ParseException::class);
        Offset::parse('4', '%N')->orFail();
    }

    public function testHours(): void
    {
        $offset = Offset::parse('1', '%h')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 1, 0, 0);


        $offset = Offset::parse('18', '%H')->orFail();
        self::assertInstanceOf(Offset::class, $offset);
        self::assertOffset($offset, 18, 0, 0);

        $this->expectException(OutOfRangeException::class);
        Offset::parse('19', '%H')->orFail();
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
