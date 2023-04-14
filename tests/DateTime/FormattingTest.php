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

    public function testToCookie(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 EST',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('EST'))->toCookie()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 EST',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('EST'))->format(IDateTime::COOKIE)->orFail()
        );
    }

    public function testToHttp(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 17:30:25 GMT',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('EST'))->toHttp()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 GMT',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::of('EST'))->format(IDateTime::HTTP)->orFail()
        );
    }

    public function testToIso8601(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toIso8601()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123Z',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->toIso8601(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->toIso8601(microseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->toIso8601()
        );
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123Z',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->format(IDateTime::ISO8601_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->format(IDateTime::ISO8601_MICROSECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->format(IDateTime::ISO8601)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25Z',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456)->format()->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25+01:30',
            (string) DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::fromTotalMinutes(90))
        );
    }

    public function testToRfc2822(): void
    {
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(2))->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0000',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(0))->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 -0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(-2))->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0000',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::utc())->toRfc2822()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(2))->format(IDateTime::RFC2822)->orFail()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0000',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(0))->format(IDateTime::RFC2822)->orFail()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 -0200',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, Offset::of(-2))->format(IDateTime::RFC2822)->orFail()
        );
        self::assertSame(
            'Thu, 25 Dec 1986 12:30:25 +0000',
            DateTime::of(1986, 12, 25, 12, 30, 25, 0, TimeZone::utc())->format(IDateTime::RFC2822)->orFail()
        );
    }

    public function testToRfc3339(): void
    {
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toRfc3339()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->toRfc3339(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->toRfc3339(microseconds: true)
        );
        self::assertSame(
            '1986-12-25T12:30:25+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->toRfc3339()
        );
        self::assertSame(
            '1986-12-25T12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::RFC3339)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->format(IDateTime::RFC3339_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->format(IDateTime::RFC3339_MICROSECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25T12:30:25+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->format(IDateTime::RFC3339)->orFail()
        );
    }

    public function testToSql(): void
    {
        self::assertSame(
            '1986-12-25 12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->toSql()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->toSql(milliseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->toSql(microseconds: true)
        );
        self::assertSame(
            '1986-12-25 12:30:25+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->toSql()
        );
        self::assertSame(
            '1986-12-25 12:30:25+03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(3))->format(IDateTime::SQL)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(0))->format(IDateTime::SQL_MILLISECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25.123456-03:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, Offset::of(-3))->format(IDateTime::SQL_MICROSECONDS)->orFail()
        );
        self::assertSame(
            '1986-12-25 12:30:25+00:00',
            DateTime::of(1986, 12, 25, 12, 30, 25, 123_456, TimeZone::utc())->format(IDateTime::SQL)->orFail()
        );
    }
}
