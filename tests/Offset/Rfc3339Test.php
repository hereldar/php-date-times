<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Generator;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class Rfc3339Test extends TestCase
{
    /**
     * @dataProvider dataForRfc3339
     */
    public function testRfc3339(
        Offset $offset,
        string $expected,
    ): void {
        self::assertEquals($offset, Offset::fromRfc3339($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::RFC3339)->orFail());

        self::assertEquals($expected, $offset->toRfc3339());
        self::assertEquals($expected, $offset->format(Offset::RFC3339)->orFail());
        self::assertEquals($expected, $offset->formatted(Offset::RFC3339));
    }

    public static function dataForRfc3339(): Generator
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
     * @dataProvider dataForRfc3339WithSeconds
     */
    public function testRfc3339WithSeconds(
        Offset $offset,
        string $expected,
    ): void {
        self::assertEquals($offset, Offset::fromRfc3339($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::RFC3339_SECONDS)->orFail());

        self::assertEquals($expected, $offset->toRfc3339());
        self::assertEquals($expected, $offset->format(Offset::RFC3339_SECONDS)->orFail());
        self::assertEquals($expected, $offset->formatted(Offset::RFC3339_SECONDS));
    }

    public static function dataForRfc3339WithSeconds(): Generator
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
        Offset::fromRfc3339('');
    }
}
