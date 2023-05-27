<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class FormattingTest extends TestCase
{
    public function testFormat(): void
    {
        self::assertSame(
            '1pm',
            LocalTime::of(13)->format('ga')->orFail()
        );
    }

    public function testToIso8601(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->toIso8601()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->toIso8601(milliseconds: true)
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->toIso8601(microseconds: true)
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::ISO8601)->orFail()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::ISO8601_MICROSECONDS)->orFail()
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->format()->orFail()
        );
        self::assertSame(
            '12:30:25',
            (string) LocalTime::of(12, 30, 25, 123_456)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 0)->toRfc2822()
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 0)->format(LocalTime::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->toRfc3339()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->toRfc3339(milliseconds: true)
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->toRfc3339(microseconds: true)
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::RFC3339)->orFail()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::RFC3339_MICROSECONDS)->orFail()
        );
    }

    public function testToSql(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->toSql()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->toSql(milliseconds: true)
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->toSql(microseconds: true)
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::SQL)->orFail()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '12:30:25.123456',
            LocalTime::of(12, 30, 25, 123_456)->format(LocalTime::SQL_MICROSECONDS)->orFail()
        );
    }
}
