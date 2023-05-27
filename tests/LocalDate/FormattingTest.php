<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

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
            LocalDate::of(1986, 12, 25)->format(LocalDate::ISO8601)->orFail()
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
            LocalDate::of(1986, 12, 25)->format(LocalDate::RFC2822)->orFail()
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
            LocalDate::of(1986, 12, 25)->format(LocalDate::RFC3339)->orFail()
        );
    }

    public function testToSql(): void
    {
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->toSql()
        );
        self::assertSame(
            '1986-12-25',
            LocalDate::of(1986, 12, 25)->format(LocalDate::SQL)->orFail()
        );
    }
}
