<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalTime::parse('1pm', '')
        );
        self::assertEquals(
            LocalTime::of(13),
            LocalTime::parse('1pm', 'ga')->orFail()
        );
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalTime::parse('1pm', [])
        );
        self::assertEquals(
            LocalTime::of(13),
            LocalTime::parse('1pm', ['ga'])->orFail()
        );
        self::assertEquals(
            LocalTime::of(13),
            LocalTime::parse('1pm', ['H:i:s', 'ga'])->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::fromIso8601('12:30:25')
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::fromIso8601('12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::fromIso8601('12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::parse('12:30:25', ILocalTime::ISO8601)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::parse('12:30:25.123', ILocalTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::parse('12:30:25.123456', ILocalTime::ISO8601_MICROSECONDS)->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            LocalTime::of(12, 30, 25),
            LocalTime::fromRfc2822('12:30:25')
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25),
            LocalTime::parse('12:30:25', ILocalTime::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::fromRfc3339('12:30:25')
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::fromRfc3339('12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::fromRfc3339('12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::parse('12:30:25', ILocalTime::RFC3339)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::parse('12:30:25.123', ILocalTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::parse('12:30:25.123456', ILocalTime::RFC3339_MICROSECONDS)->orFail()
        );
    }

    public function testFromSql(): void
    {
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::fromSql('12:30:25')
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::fromSql('12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::fromSql('12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 0),
            LocalTime::parse('12:30:25', ILocalTime::SQL)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_000),
            LocalTime::parse('12:30:25.123', ILocalTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalTime::of(12, 30, 25, 123_456),
            LocalTime::parse('12:30:25.123456', ILocalTime::SQL_MICROSECONDS)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalTime::parse('', 'H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('', $e->string());
            self::assertSame('!H:i:s', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalTime::parse('15:00:00+02:00', 'H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('15:00:00+02:00', $e->string());
            self::assertSame('!H:i:s', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalTime::parse('4', 'b')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('!b', $e->format());
            self::assertSame('The format separator does not match', $e->error());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalTime::parse('4', 'T')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('!T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
