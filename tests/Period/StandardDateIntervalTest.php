<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use DateInterval as StandardDateInterval;
use Generator;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class StandardDateIntervalTest extends TestCase
{
    /**
     * @dataProvider dataForStandardDateInterval
     */
    public function testStandardDateInterval(
        Period $period,
        StandardDateInterval $expected,
    ): void {
        self::assertEquals($expected, $period->toStandard());

        $period = $period->negated();
        $expected = Period::fromStandard($expected)->negated()->toStandard();

        self::assertEquals($expected, $period->toStandard());
    }

    public function dataForStandardDateInterval(): Generator
    {
        yield [
            Period::of(0),
            new StandardDateInterval('PT0S'),
        ];
        yield [
            Period::zero(),
            new StandardDateInterval('PT0S'),
        ];
        yield [
            Period::of(1),
            new StandardDateInterval('P1Y'),
        ];
        yield [
            Period::of(0, 1),
            new StandardDateInterval('P1M'),
        ];
        yield [
            Period::of(0, 0, 1),
            new StandardDateInterval('P7D'),
        ];
        yield [
            Period::of(0, 0, 0, 1),
            new StandardDateInterval('P1D'),
        ];
        yield [
            Period::of(1, 2, 0, 3),
            new StandardDateInterval('P1Y2M3D'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 1),
            new StandardDateInterval('PT1H'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 1),
            new StandardDateInterval('PT1M'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 0, 1),
            new StandardDateInterval('PT1S'),
        ];
        $period = Period::of(0, 0, 0, 0, 0, 0, 0, 12);
        $interval = new StandardDateInterval('PT0S');
        $interval->f = .012;
        yield [$period, $interval];

        $period = Period::of(0, 0, 0, 0, 0, 0, 0, 0, 12340);
        $interval = new StandardDateInterval('PT0S');
        $interval->f = .01234;
        yield [$period, $interval];

        $period = Period::of(0, 0, 0, 0, 1, 2, 3, 0, 4);
        $interval = new StandardDateInterval('PT1H2M3S');
        $interval->f = .000004;
        yield [$period, $interval];

        $period = Period::of(1, 2, 0, 3, 4, 5, 6, 0, 7);
        $interval = new StandardDateInterval('P1Y2M3DT4H5M6S');
        $interval->f = .000007;
        yield [$period, $interval];
    }
}
