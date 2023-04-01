<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class FormattingTest extends TestCase
{
    public function testFormat(): void
    {
        self::assertSame(
            '25th of December, 1986, 1pm',
            DateTime::of(1986, 12, 25, 13)->format('jS \o\f F, Y, ga')->orFail()
        );
    }

    public function testToIso8601(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25)->toIso8601()
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25)->format(IDateTime::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25)->format()->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            (string) DateTime::of(1986, 12, 25, 12, 30, 25)
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 -0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(-2))->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 -0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(-2))->format(IDateTime::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toRfc3339()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toRfc3339(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::RFC3339)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::RFC3339_EXTENDED)->orFail()
        );
    }

    public function testToSql(): void
    {
        self::assertSame(
            '1986-12-25 12:30:25 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toSql()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toSql(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toSql(microseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::SQL)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456 +03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::SQL_MICROSECONDS)->orFail()
        );
    }

    public function testToAtom(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25)->format(IDateTime::ATOM)->orFail()
        );
    }

    public function testToCookie(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-1986 12:30:25 EST',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('EST'))->format(IDateTime::COOKIE)->orFail()
        );
    }

    public function testToRfc822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86 12:30:25 -0030',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(0, -30))->format(IDateTime::RFC822)->orFail()
        );
    }

    public function testToRfc850(): void
    {
        self::assertSame(
            'Thursday, 25-Dec-86 12:30:25 MDT',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, 'MDT')->format(IDateTime::RFC850)->orFail()
        );
    }

    public function testToRfc1036(): void
    {
        self::assertSame(
            'Thu, 25 Dec 86 12:30:25 -0030',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, '-00:30')->format(IDateTime::RFC1036)->orFail()
        );
    }

    public function testToRfc1123(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0230',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('+02:30'))->format(IDateTime::RFC1123)->orFail()
        );
    }

    public function testToRfc7231(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 GMT',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::utc())->format(IDateTime::RFC7231)->orFail()
        );
    }

    public function testToRss(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 -0030',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalSeconds(-1_800))->format(IDateTime::RSS)->orFail()
        );
    }

    public function testToW3c(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25+02:30',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::fromTotalSeconds(9_000))->format(IDateTime::W3C)->orFail()
        );
    }
}
