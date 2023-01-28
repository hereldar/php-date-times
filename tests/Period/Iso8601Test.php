<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Generator;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class Iso8601Test extends TestCase
{
    /**
     * @dataProvider dataForIso8601
     */
    public function testIso8601(
        Period $period,
        string $expected,
        bool $reversible = true,
    ): void {
        self::assertEquals($period, Period::fromIso8601($expected));
        self::assertEquals($period, Period::parse($expected, IPeriod::ISO8601));
        self::assertEquals($period, Period::parse($expected));

        if ($reversible) {
            self::assertEquals($expected, $period->toIso8601());
            self::assertEquals($expected, $period->format(IPeriod::ISO8601));
            self::assertEquals($expected, $period->format());
            self::assertEquals($expected, (string) $period);
        }

        $period = $period->negated();
        $expected = Period::fromIso8601($expected)->negated()->toIso8601();

        self::assertEquals($period, Period::fromIso8601($expected));
        self::assertEquals($period, Period::parse($expected, IPeriod::ISO8601));
        self::assertEquals($period, Period::parse($expected));

        if ($reversible) {
            self::assertEquals($expected, $period->toIso8601());
            self::assertEquals($expected, $period->format(IPeriod::ISO8601));
            self::assertEquals($expected, $period->format());
            self::assertEquals($expected, (string) $period);
        }
    }

    public function dataForIso8601(): Generator
    {
        yield [
            Period::of(0),
            'PT0S',
        ];
        yield [
            Period::zero(),
            'PT0S',
        ];
        yield [
            Period::of(1),
            'P1Y',
        ];
        yield [
            Period::of(0, 1),
            'P1M',
        ];
        yield [
            Period::of(0, 0, 1),
            'P7D',
        ];
        yield [
            Period::of(0, 0, 0, 1),
            'P1D',
        ];
        yield [
            Period::of(1, 2, 0, 3),
            'P1Y2M3D',
        ];
        yield [
            Period::of(0, 0, 0, 0, 1),
            'PT1H',
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 1),
            'PT1M',
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 0, 1),
            'PT1S',
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 0, 0, 12),
            'PT0.012S',
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 0, 0, 0, 12340),
            'PT0.01234S',
        ];
        yield [
            Period::of(0, 0, 0, 0, 1, 2, 3, 0, 4),
            'PT1H2M3.000004S',
        ];
        yield [
            Period::of(1, 2, 0, 3, 4, 5, 6, 0, 7),
            'P1Y2M3DT4H5M6.000007S',
        ];
        yield [
            Period::of(-1, -2, 0, -3, -4, -5, -6, 0, -7),
            'P-1Y-2M-3DT-4H-5M-6.000007S',
            false,
        ];
        yield [
            Period::of(-1, -2, 0, -3, -4, -5, -6, 0, -7),
            '-P1Y2M3DT4H5M6.000007S',
        ];
    }
}
