<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Generator;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class Rfc2822Test extends TestCase
{
    /**
     * @dataProvider dataForRfc2822
     */
    public function testRfc2822(
        Offset $offset,
        string $expected,
    ): void {
        self::assertEquals($offset, Offset::fromRfc2822($expected));
        self::assertEquals($offset, Offset::parse($expected, Offset::RFC2822)->orFail());

        self::assertEquals($expected, $offset->toRfc2822());
        self::assertEquals($expected, $offset->format(Offset::RFC2822)->orFail());
        self::assertEquals($expected, $offset->formatted(Offset::RFC2822));
    }

    public static function dataForRfc2822(): Generator
    {
        yield [
            Offset::of(0),
            '+0000',
        ];
        yield [
            Offset::zero(),
            '+0000',
        ];
        yield [
            Offset::of(1),
            '+0100',
        ];
        yield [
            Offset::of(0, 1),
            '+0001',
        ];
        yield [
            Offset::of(1, 2),
            '+0102',
        ];
        yield [
            Offset::of(-1, -2),
            '-0102',
        ];
    }

    public function testParseException(): void
    {
        $this->expectException(ParseException::class);
        Offset::fromRfc2822('');
    }
}
