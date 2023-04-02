<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use DateInterval as NativeDateInterval;
use Generator;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class NativeDateIntervalTest extends TestCase
{
    /**
     * @dataProvider dataForNativeDateInterval
     */
    public function testNativeDateInterval(
        Period $period,
        NativeDateInterval $nativeDateInterval,
    ): void {
        self::assertEquals($nativeDateInterval, $period->toNative());

        $period = $period->negated();
        $nativeDateInterval = Period::fromNative($nativeDateInterval)->negated()->toNative();

        self::assertEquals($nativeDateInterval, $period->toNative());
    }

    public static function dataForNativeDateInterval(): Generator
    {
        yield [
            Period::of(0),
            new NativeDateInterval('PT0S'),
        ];
        yield [
            Period::zero(),
            new NativeDateInterval('PT0S'),
        ];
        yield [
            Period::of(1),
            new NativeDateInterval('P1Y'),
        ];
        yield [
            Period::of(0, 1),
            new NativeDateInterval('P1M'),
        ];
        yield [
            Period::of(0, 0, 1),
            new NativeDateInterval('P7D'),
        ];
        yield [
            Period::of(0, 0, 0, 1),
            new NativeDateInterval('P1D'),
        ];
        yield [
            Period::of(1, 2, 0, 3),
            new NativeDateInterval('P1Y2M3D'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 1),
            new NativeDateInterval('PT1H'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 1),
            new NativeDateInterval('PT1M'),
        ];
        yield [
            Period::of(0, 0, 0, 0, 0, 0, 1),
            new NativeDateInterval('PT1S'),
        ];
        $period = Period::of(0, 0, 0, 0, 0, 0, 0, 12);
        $interval = new NativeDateInterval('PT0S');
        $interval->f = .012;
        yield [$period, $interval];

        $period = Period::of(0, 0, 0, 0, 0, 0, 0, 0, 12340);
        $interval = new NativeDateInterval('PT0S');
        $interval->f = .01234;
        yield [$period, $interval];

        $period = Period::of(0, 0, 0, 0, 1, 2, 3, 0, 4);
        $interval = new NativeDateInterval('PT1H2M3S');
        $interval->f = .000004;
        yield [$period, $interval];

        $period = Period::of(1, 2, 0, 3, 4, 5, 6, 0, 7);
        $interval = new NativeDateInterval('P1Y2M3DT4H5M6S');
        $interval->f = .000007;
        yield [$period, $interval];
    }
}
