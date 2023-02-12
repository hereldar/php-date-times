<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class ParsingTest extends TestCase
{
    public function testParse(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('25th of December, 1975', 'jS \o\f F, Y')->orFail()
        );
    }

    public function testFromIso8601(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::fromIso8601('1975-12-25')
        );
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25', ILocalDate::ISO8601)->orFail()
        );
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25')->orFail()
        );
    }

    public function testFromRfc2822(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::fromRfc2822('Thu, 25 Dec 1975')
        );
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 1975', ILocalDate::RFC2822)->orFail()
        );
    }

    public function testFromRfc3339(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::fromRfc3339('1975-12-25')
        );
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25', ILocalDate::RFC3339)->orFail()
        );
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25', ILocalDate::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testFromAtom(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25', ILocalDate::ATOM)->orFail()
        );
    }

    public function testFromCookie(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thursday, 25-Dec-1975', ILocalDate::COOKIE)->orFail()
        );
    }

    public function testFromRfc822(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 75', ILocalDate::RFC822)->orFail()
        );
    }

    public function testFromRfc850(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thursday, 25-Dec-75', ILocalDate::RFC850)->orFail()
        );
    }

    public function testFromRfc1036(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 75', ILocalDate::RFC1036)->orFail()
        );
    }

    public function testFromRfc1123(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 1975', ILocalDate::RFC1123)->orFail()
        );
    }

    public function testFromRfc7231(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 1975', ILocalDate::RFC7231)->orFail()
        );
    }

    public function testFromRss(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('Thu, 25 Dec 1975', ILocalDate::RSS)->orFail()
        );
    }

    public function testFromW3c(): void
    {
        self::assertEquals(
            LocalDate::of(1975, 12, 25),
            LocalDate::parse('1975-12-25', ILocalDate::W3C)->orFail()
        );
    }
}
