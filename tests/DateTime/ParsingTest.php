<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 13),
            DateTime::parse('25th of December, 1986, 1pm', 'jS \o\f F, Y, ga')->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::fromIso8601('1986-12-25T12:30:25Z')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25Z', IDateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25Z')->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::fromRfc2822('Thu, 25 Dec 1986 12:30:25 +0200')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(120)),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 +0200', IDateTime::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(-120)),
            DateTime::fromRfc3339('1986-12-25T12:30:25-02:00')
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(-120)),
            DateTime::fromRfc3339('1986-12-25T12:30:25.123-02:00', milliseconds: true)
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalMinutes(-120)),
            DateTime::parse('1986-12-25T12:30:25-02:00', IDateTime::RFC3339)->orFail()
        );
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_000, Offset::fromTotalMinutes(-120)),
            DateTime::parse('1986-12-25T12:30:25.123-02:00', IDateTime::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testFromAtom(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25),
            DateTime::parse('1986-12-25T12:30:25+00:00', IDateTime::ATOM)->orFail()
        );
    }

    public function testFromCookie(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('+05:00')),
            DateTime::parse('Thursday, 25-Dec-1986 12:30:25 +05', IDateTime::COOKIE)->orFail()
        );
    }

    public function testFromRfc822(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalSeconds(1800)),
            DateTime::parse('Thu, 25 Dec 86 12:30:25 +0030', IDateTime::RFC822)->orFail()
        );
    }

    public function testFromRfc850(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, '-03:00'),
            DateTime::parse('Thursday, 25-Dec-86 12:30:25 -03', IDateTime::RFC850)->orFail()
        );
    }

    public function testFromRfc1036(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, '+00:30'),
            DateTime::parse('Thu, 25 Dec 86 12:30:25 +0030', IDateTime::RFC1036)->orFail()
        );
    }

    public function testFromRfc1123(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('-02:30')),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 -0230', IDateTime::RFC1123)->orFail()
        );
    }

    public function testFromRfc7231(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, 'UTC'),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 GMT', IDateTime::RFC7231)->orFail()
        );
    }

    public function testFromRss(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromIso8601('-00:30')),
            DateTime::parse('Thu, 25 Dec 1986 12:30:25 -0030', IDateTime::RSS)->orFail()
        );
    }

    public function testFromW3c(): void
    {
        self::assertEquals(
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromIso8601('+02:30')),
            DateTime::parse('1986-12-25T12:30:25+02:30', IDateTime::W3C)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            DateTime::parse('', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            DateTime::parse('1970-01-01 15:00:00+02:00', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('1970-01-01 15:00:00+02:00', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            DateTime::parse('4', 'b')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('b', $e->format());
            self::assertSame('The format separator does not match', $e->error());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            DateTime::parse('4', 'T')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
