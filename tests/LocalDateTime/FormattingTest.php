<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class FormattingTest extends TestCase
{
    public function testFormat(): void
    {
        self::assertSame(
            '25th of December, 1986, 1pm',
            LocalDateTime::of(1986, 12, 25, 13)->format('jS \o\f F, Y, ga')->orFail()
        );
    }

    public function testToIso8601(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->toIso8601()
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format()->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            (string) LocalDateTime::of(1986, 12, 25, 12, 30, 25)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toRfc3339()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toRfc3339(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(ILocalDateTime::RFC3339)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(ILocalDateTime::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testToSql(): void
    {
        self::assertSame(
            '1986-12-25 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toSql()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toSql(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toSql(microseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(ILocalDateTime::SQL)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(ILocalDateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(ILocalDateTime::SQL_MICROSECONDS)->orFail()
        );
    }

    public function testToAtom(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::ATOM)->orFail()
        );
    }

    public function testToCookie(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::COOKIE)->orFail()
        );
    }

    public function testToRfc822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC822)->orFail()
        );
    }

    public function testToRfc850(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-86 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC850)->orFail()
        );
    }

    public function testToRfc1036(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC1036)->orFail()
        );
    }

    public function testToRfc1123(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC1123)->orFail()
        );
    }

    public function testToRfc7231(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RFC7231)->orFail()
        );
    }

    public function testToRss(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::RSS)->orFail()
        );
    }

    public function testToW3c(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25)->format(ILocalDateTime::W3C)->orFail()
        );
    }
}
