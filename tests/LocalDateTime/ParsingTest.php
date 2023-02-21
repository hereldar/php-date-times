<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 13),
            LocalDateTime::parse('25th of December, 1986, 1pm', 'jS \o\f F, Y, ga')->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::fromIso8601('1986-12-25T12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::ISO8601)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('1986-12-25T12:30:25')->orFail()
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
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::fromRfc3339('1986-12-25T12:30:25')
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::fromRfc3339('1986-12-25T12:30:25.123', milliseconds: true)
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::RFC3339)->orFail()
        );
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_000),
            LocalDateTime::parse('1986-12-25T12:30:25.123', ILocalDateTime::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testFromAtom(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::ATOM)->orFail()
        );
    }

    public function testFromCookie(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thursday, 25-Dec-1986 12:30:25', ILocalDateTime::COOKIE)->orFail()
        );
    }

    public function testFromRfc822(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 86 12:30:25', ILocalDateTime::RFC822)->orFail()
        );
    }

    public function testFromRfc850(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thursday, 25-Dec-86 12:30:25', ILocalDateTime::RFC850)->orFail()
        );
    }

    public function testFromRfc1036(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 86 12:30:25', ILocalDateTime::RFC1036)->orFail()
        );
    }

    public function testFromRfc1123(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 1986 12:30:25', ILocalDateTime::RFC1123)->orFail()
        );
    }

    public function testFromRfc7231(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 1986 12:30:25', ILocalDateTime::RFC7231)->orFail()
        );
    }

    public function testFromRss(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('Thu, 25 Dec 1986 12:30:25', ILocalDateTime::RSS)->orFail()
        );
    }

    public function testFromW3c(): void
    {
        self::assertEquals(
            LocalDateTime::of(1986, 12, 25, 12, 30, 25),
            LocalDateTime::parse('1986-12-25T12:30:25', ILocalDateTime::W3C)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            LocalDateTime::parse('', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            LocalDateTime::parse('1970-01-01 15:00:00+02:00', 'Y-m-d H:i:s')->orFail();
        } catch (ParseException $e) {
            self::assertSame('1970-01-01 15:00:00+02:00', $e->string());
            self::assertSame('Y-m-d H:i:s', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            LocalDateTime::parse('4', 'b')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('b', $e->format());
            self::assertSame('The format separator does not match', $e->error());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            LocalDateTime::parse('4', 'T')->orFail();
        } catch (ParseException $e) {
            self::assertSame('4', $e->string());
            self::assertSame('T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
