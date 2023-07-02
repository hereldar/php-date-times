<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;
use InvalidArgumentException;
use Throwable;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 13),
            DateTime::parse('25th of December, 1986, 1pm', 'jS \o\f F, Y, ga')->orFail()
        );
        self::assertException(
            InvalidArgumentException::class,
            fn () => DateTime::parse('25th of December, 1986, 1pm', [])
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 13),
            DateTime::parse('25th of December, 1986, 1pm', ['jS \o\f F, Y, ga'])->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 13),
            DateTime::parse('25th of December, 1986, 1pm', ['Y-m-d H:i:s', 'jS \o\f F, Y, ga'])->orFail()
        );
    }

    public function testFromCookie(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::utc()),
            DateTime::fromCookie('Thu, 25 Dec 1986 08:30:25 GMT')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('CEST')),
            DateTime::fromCookie('Thursday, 25-Dec-86 08:30:25 CEST')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('+02:00')),
            DateTime::fromCookie('Thursday, 25-Dec-1986 08:30:25 CEST')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::utc()),
            DateTime::fromCookie('Thu Dec 25 8:30:25 1986')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('+05:00')),
            DateTime::fromCookie('Thu Dec 25 08:30:25 1986 +05')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('+05:00')),
            DateTime::parse('Thu, 25 Dec 1986 08:30:25 +05:00', DateTime::COOKIE)->orFail()
        );
    }

    public function testFromHttp(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::utc()),
            DateTime::fromHttp('Thu, 25 Dec 1986 08:30:25 GMT')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('GMT')),
            DateTime::fromHttp('Thursday, 25-Dec-86 08:30:25 GMT')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('+00:00')),
            DateTime::fromHttp('Thursday, 25-Dec-1986 08:30:25 GMT')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::utc()),
            DateTime::fromHttp('Thu Dec 25 8:30:25 1986')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::utc()),
            DateTime::fromHttp('Thu Dec 25 08:30:25 1986 GMT')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 8, 30, 25, 0, TimeZone::of('+00:00')),
            DateTime::parse('Thu, 25 Dec 1986 08:30:25 GMT', DateTime::HTTP)->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromIso8601('1986-12-25T12:30:25Z')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::fromIso8601('1986-12-25T12:30:25.123Z', milliseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc()),
            DateTime::fromIso8601('1986-12-25T12:30:25.123456Z', microseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromIso8601('1986-12-25T12:30:25+00:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::fromIso8601('1986-12-25T12:30:25+02:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::fromIso8601('1986-12-25T12:30:25.123+00:00', milliseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::fromTotalMinutes(-120)),
            DateTime::fromIso8601('1986-12-25T12:30:25.123456-02:00', microseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25Z', DateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::parse('1986-12-25T12:30:25.123Z', DateTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc()),
            DateTime::parse('1986-12-25T12:30:25.123456Z', DateTime::ISO8601_MICROSECONDS)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25+00:00', DateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::parse('1986-12-25T12:30:25+02:00', DateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::parse('1986-12-25T12:30:25.123+00:00', DateTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::fromTotalMinutes(-120)),
            DateTime::parse('1986-12-25T12:30:25.123456-02:00', DateTime::ISO8601_MICROSECONDS)->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25 +0000')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25 +0200')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(0)),
            DateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25 +0000')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(-120)),
            DateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25 -0200')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 +0000', DateTime::RFC2822)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 +0200', DateTime::RFC2822)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(0)),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 +0000', DateTime::RFC2822)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(-120)),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 -0200', DateTime::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromRfc3339('1986-12-25T12:30:25+00:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::fromRfc3339('1986-12-25T12:30:25+02:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::fromRfc3339('1986-12-25T12:30:25.123+00:00', milliseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::fromTotalMinutes(-120)),
            DateTime::fromRfc3339('1986-12-25T12:30:25.123456-02:00', microseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25+00:00', DateTime::RFC3339)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::parse('1986-12-25T12:30:25+02:00', DateTime::RFC3339)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(0)),
            DateTime::parse('1986-12-25T12:30:25.123+00:00', DateTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::fromTotalMinutes(-120)),
            DateTime::parse('1986-12-25T12:30:25.123456-02:00', DateTime::RFC3339_MICROSECONDS)->orFail()
        );
    }

    public function testFromSql(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromSql('1986-12-25 12:30:25+00:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(2)),
            DateTime::fromSql('1986-12-25 12:30:25+02:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::of(0)),
            DateTime::fromSql('1986-12-25 12:30:25.123+00:00', milliseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-2)),
            DateTime::fromSql('1986-12-25 12:30:25.123456-02:00', microseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25 12:30:25+00:00', DateTime::SQL)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(2)),
            DateTime::parse('1986-12-25 12:30:25+02:00', DateTime::SQL)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::of(0)),
            DateTime::parse('1986-12-25 12:30:25.123+00:00', DateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-2)),
            DateTime::parse('1986-12-25 12:30:25.123456-02:00', DateTime::SQL_MICROSECONDS)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            DateTime::parse('', 'Y-m-d H:i:s')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            DateTime::parse('1970-01-01 15:00:00+02:00', 'Y-m-d H:i:s')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('1970-01-01 15:00:00+02:00', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            DateTime::parse('4', 'b')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('b', $e->format());
            self::assertSame('The format separator does not match', $e->error());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            DateTime::parse('4', 'T')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
