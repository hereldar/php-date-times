<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class FormattingTest extends TestCase
{
    public function testFormat(): void
    {
        self::assertSame(
            '25th of December, 1986',
            LocalDate::of(1986, 12, 25)->format('jS \o\f F, Y')->orFail()
        );
    }

    public function testToIso8601(): void
    {
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->toIso8601()
        );
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format()->orFail()
        );
        self::assertSame(
            '1986-12-25',
            (string) LocalDate::of(1986, 12, 25)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986',
            LocalDate::of(1986, 12, 25)->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->toRfc3339()
        );
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC3339)->orFail()
        );
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testToAtom(): void
    {
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::ATOM)->orFail()
        );
    }

    public function testToCookie(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-1986',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::COOKIE)->orFail()
        );
    }

    public function testToRfc822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC822)->orFail()
        );
    }

    public function testToRfc850(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-86',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC850)->orFail()
        );
    }

    public function testToRfc1036(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC1036)->orFail()
        );
    }

    public function testToRfc1123(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC1123)->orFail()
        );
    }

    public function testToRfc7231(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RFC7231)->orFail()
        );
    }

    public function testToRss(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::RSS)->orFail()
        );
    }

    public function testToW3c(): void
    {
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(ILocalDate::W3C)->orFail()
        );
    }
}
