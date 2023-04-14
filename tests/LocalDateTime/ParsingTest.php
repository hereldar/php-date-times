<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalDateTime::parse('25th of December, 1986, 1pm', '')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 13),
            LocalDateTime::parse('25th of December, 1986, 1pm', 'jS \o\f F, Y, ga')->orFail()
        );
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalDateTime::parse('25th of December, 1986, 1pm', [])
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 13),
            LocalDateTime::parse('25th of December, 1986, 1pm', ['jS \o\f F, Y, ga'])->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 13),
            LocalDateTime::parse('25th of December, 1986, 1pm', ['Y-m-d H:i:s', 'jS \o\f F, Y, ga'])->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::fromIso8601('1986-12-25T12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::fromIso8601('1986-12-25T12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::fromIso8601('1986-12-25T12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::parse('1986-12-25T12:30:25.123', ILocalDateTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::parse('1986-12-25T12:30:25.123456', ILocalDateTime::ISO8601_MICROSECONDS)->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 1986 12:30:25', ILocalDateTime::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::fromRfc3339('1986-12-25T12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::fromRfc3339('1986-12-25T12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::fromRfc3339('1986-12-25T12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::RFC3339)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::parse('1986-12-25T12:30:25.123', ILocalDateTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::parse('1986-12-25T12:30:25.123456', ILocalDateTime::RFC3339_MICROSECONDS)->orFail()
        );
    }

    public function testFromSql(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::fromSql('1986-12-25 12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::fromSql('1986-12-25 12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::fromSql('1986-12-25 12:30:25.123456', microseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0),
            LocalDateTime::parse('1986-12-25 12:30:25', ILocalDateTime::SQL)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::parse('1986-12-25 12:30:25.123', ILocalDateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456),
            LocalDateTime::parse('1986-12-25 12:30:25.123456', ILocalDateTime::SQL_MICROSECONDS)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDateTime::parse('', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('', $e->string());
            self::assertSame('!Y-m-d H:i:s', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDateTime::parse('1970-01-01 15:00:00+02:00', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('1970-01-01 15:00:00+02:00', $e->string());
            self::assertSame('!Y-m-d H:i:s', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDateTime::parse('4', 'b')->orFail();
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
            LocalDateTime::parse('4', 'T')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('!T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
