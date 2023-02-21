<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\Interfaces\ILocalTime;
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
            LocalTime::of(12, 30, 25)->toIso8601()
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::ISO8601)->orFail()
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format()->orFail()
        );
        self::assertSame(
            '12:30:25',
            (string) LocalTime::of(12, 30, 25)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->toRfc2822()
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->toRfc3339()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_000)->toRfc3339(milliseconds: true)
        );
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC3339)->orFail()
        );
        self::assertSame(
            '12:30:25.123',
            LocalTime::of(12, 30, 25, 123_000)->format(ILocalTime::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testToAtom(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::ATOM)->orFail()
        );
    }

    public function testToCookie(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::COOKIE)->orFail()
        );
    }

    public function testToRfc822(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC822)->orFail()
        );
    }

    public function testToRfc850(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC850)->orFail()
        );
    }

    public function testToRfc1036(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC1036)->orFail()
        );
    }

    public function testToRfc1123(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC1123)->orFail()
        );
    }

    public function testToRfc7231(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RFC7231)->orFail()
        );
    }

    public function testToRss(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::RSS)->orFail()
        );
    }

    public function testToW3c(): void
    {
        self::assertSame(
            '12:30:25',
            LocalTime::of(12, 30, 25)->format(ILocalTime::W3C)->orFail()
        );
    }
}
