<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;
use Throwable;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalDate::parse('25th of December, 1986', '')
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('25th of December, 1986', 'jS \o\f F, Y')->orFail()
        );
        self::assertException(
            InvalidArgumentException::class,
            fn ()  => LocalDate::parse('25th of December, 1986', [])
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('25th of December, 1986', ['jS \o\f F, Y'])->orFail()
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('25th of December, 1986', ['Y-m-d', 'jS \o\f F, Y'])->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::fromIso8601('1986-12-25')
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('1986-12-25', ILocalDate::ISO8601)->orFail()
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('1986-12-25')->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::fromRfc2822('Thu, 25 Dec 1986')
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('Thu, 25 Dec 1986', ILocalDate::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::fromRfc3339('1986-12-25')
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('1986-12-25', ILocalDate::RFC3339)->orFail()
        );
    }

    public function testFromSql(): void
    {
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::fromSql('1986-12-25')
        );
        self::assertEquals(
            LocalDate::of(1986, 12, 25),
            LocalDate::parse('1986-12-25', ILocalDate::SQL)->orFail()
        );
    }

    public function testEmptyString(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDate::parse('', 'Y-m-d')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('', $e->string());
            self::assertSame('!Y-m-d', $e->format());
            self::assertSame('Not enough data available to satisfy format', $e->error());
        }
    }

    public function testTrailingData(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDate::parse('1970-01-01', 'Y-m')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('1970-01-01', $e->string());
            self::assertSame('!Y-m', $e->format());
            self::assertSame('Trailing data', $e->error());
        }
    }

    public function testInvalidFormat(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDate::parse('4', 'b')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('!b', $e->format());
            self::assertSame('The format separator does not match', $e->error());
        }
    }

    public function testInvalidSubstitute(): void
    {
        try {
            /** @psalm-suppress UnusedMethodCall */
            LocalDate::parse('4', 'T')->orFail();
        } catch (Throwable $e) {
            self::assertInstanceOf(ParseException::class, $e);
            self::assertSame('4', $e->string());
            self::assertSame('!T', $e->format());
            self::assertSame('The timezone could not be found in the database', $e->error());
        }
    }
}
