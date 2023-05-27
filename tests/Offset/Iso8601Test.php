<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Generator;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class Iso8601Test extends TestCase
{
    /**
     * @dataProvider dataForIso8601
     */
    public function testIso8601(
        Offset $offset,
        string $expected,
        bool $reversible = true,
    ): void {
        self::assertEquals($offset, Offset::fromIso8601($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::ISO8601)->orFail());
        self::assertEquals($offset, Offset::parse($expected)->orFail());

        if ($reversible) {
            self::assertEquals($expected, $offset->toIso8601());
            self::assertEquals($expected, $offset->format(Offset::ISO8601)->orFail());
            self::assertEquals($expected, $offset->format()->orFail());
            self::assertEquals($expected, (string) $offset);
        }

        $offset = $offset->negated();
        $expected = Offset::fromIso8601($expected)->negated()->toIso8601();

        self::assertEquals($offset, Offset::fromIso8601($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::ISO8601)->orFail());
        self::assertEquals($offset, Offset::parse($expected)->orFail());

        if ($reversible) {
            self::assertEquals($expected, $offset->toIso8601());
            self::assertEquals($expected, $offset->format(Offset::ISO8601)->orFail());
            self::assertEquals($expected, $offset->format()->orFail());
            self::assertEquals($expected, (string) $offset);
        }
    }

    public static function dataForIso8601(): Generator
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
            Offset::of(0, 0, 1),
            '+00:00:01',
        ];
        yield [
            Offset::of(1, 2),
            '+01:02',
        ];
        yield [
            Offset::of(1, 2, 3),
            '+01:02:03',
        ];
        yield [
            Offset::of(-1, -2),
            '-01:02',
        ];
        yield [
            Offset::of(-1, -2, -3),
            '-01:02:03',
        ];
    }

    public function testParseException(): void
    {
        $this->expectException(ParseException::class);
        Offset::fromIso8601('');
    }
}
