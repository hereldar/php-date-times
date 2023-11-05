<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Generator;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class SqlTest extends TestCase
{
    /**
     * @dataProvider dataForSql
     */
    public function testSql(
        Offset $offset,
        string $expected,
    ): void {
        self::assertEquals($offset, Offset::fromSql($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::SQL)->orFail());

        self::assertEquals($expected, $offset->toSql());
        self::assertEquals($expected, $offset->format(Offset::SQL)->orFail());
        self::assertEquals($expected, $offset->formatted(Offset::SQL));
    }

    /**
     * @return Generator<int, array{Offset, string}>
     */
    public static function dataForSql(): Generator
    {
        yield [
            Offset::of(0),
            '+00:00',
        ];
        yield [
            Offset::zero(),
            '+00:00',
        ];
        yield [
            Offset::of(1),
            '+01:00',
        ];
        yield [
            Offset::of(0, 1),
            '+00:01',
        ];
        yield [
            Offset::of(1, 2),
            '+01:02',
        ];
        yield [
            Offset::of(-1, -2),
            '-01:02',
        ];
    }

    /**
     * @dataProvider dataForSqlWithSeconds
     */
    public function testSqlWithSeconds(
        Offset $offset,
        string $expected,
    ): void {
        self::assertEquals($offset, Offset::fromSql($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::SQL_SECONDS)->orFail());

        self::assertEquals($expected, $offset->toSql());
        self::assertEquals($expected, $offset->format(Offset::SQL_SECONDS)->orFail());
        self::assertEquals($expected, $offset->formatted(Offset::SQL_SECONDS));
    }

    /**
     * @return Generator<int, array{Offset, string}>
     */
    public static function dataForSqlWithSeconds(): Generator
    {
        yield [
            Offset::of(0, 0, 1),
            '+00:00:01',
        ];
        yield [
            Offset::of(1, 2, 3),
            '+01:02:03',
        ];
        yield [
            Offset::of(-1, -2, -3),
            '-01:02:03',
        ];
    }

    public function testParseException(): void
    {
        $this->expectException(ParseException::class);
        Offset::fromSql('');
    }
}
