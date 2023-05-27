<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

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
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toIso8601()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toIso8601(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toIso8601(microseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::ISO8601_MICROSECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format()->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            (string) LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0)->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 0)->format(LocalDateTime::RFC2822)->orFail()
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
            '1986-12-25T12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->toRfc3339(microseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::RFC3339)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::RFC3339_MICROSECONDS)->orFail()
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
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::SQL)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456',
            LocalDateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format(LocalDateTime::SQL_MICROSECONDS)->orFail()
        );
    }
}
