<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Generator;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class NormalizationTest extends TestCase
{
    /**
     * @dataProvider dataForOverflowedValues
     */
    public function testNormalizeOverflowedValues(
        Period $period,
        Period $expected,
    ): void {
        self::assertEquals($expected, $period->normalized());
        self::assertFalse($period->isNegative());

        $period = $period->negated();
        $expected = $expected->negated();

        self::assertEquals($expected, $period->normalized());
        self::assertTrue($period->isNegative());
    }

    /**
     * @return Generator<int, array{Period, Period}>
     */
    public static function dataForOverflowedValues(): Generator
    {
        yield [
            Period::of(seconds: 3600),
            Period::of(hours: 1),
        ];
        yield [
            Period::of(seconds: 10000),
            Period::of(hours: 2, minutes: 46, seconds: 40),
        ];
        yield [
            Period::of(days: 1276),
            Period::of(years: 3, months: 6, days: 16),
        ];
        yield [
            Period::of(days: 47, hours: 14),
            Period::of(months: 1, days: 17, hours: 14),
        ];
        yield [
            Period::of(years: 2, months: 123, weeks: 5, days: 6, hours: 47, minutes: 160, seconds: 217),
            Period::of(years: 12, months: 4, days: 13, hours: 1, minutes: 43, seconds: 37),
        ];
        yield [
            Period::of(years: 94, months: 11, days: 24, hours: 3848, microseconds: 7900),
            Period::of(years: 95, months: 5, days: 4, hours: 8, microseconds: 7900),
            // 'P95Y5M4DT8H0.0079S',
        ];
        yield [
            Period::of(milliseconds: 1040, microseconds: 3012),
            Period::of(seconds: 1, microseconds: 43012),
            // 'PT1.043012S',
        ];
    }

    /**
     * @dataProvider dataForMixedSigns
     */
    public function testNormalizeWithMixedSigns(
        Period $period,
        string $expected,
    ): void {
        $expected = Period::fromIso8601($expected);

        self::assertEquals($expected, $period->normalized());

        $period = $period->negated();
        $expected = $expected->negated();

        self::assertEquals($expected, $period->normalized());
    }

    /**
     * @return Generator<int, array{Period, string}>
     */
    public static function dataForMixedSigns(): Generator
    {
        yield [
            Period::of(hours: 1, minutes: -30),
            'PT30M',
        ];
        yield [
            Period::of(hours: 1, minutes: -90),
            '-PT30M',
        ];
        yield [
            Period::of(hours: 1, minutes: -90, seconds: 3660),
            'PT31M',
        ];
        yield [
            Period::of(hours: 1, minutes: -90, seconds: 3540),
            'PT29M',
        ];
        yield [
            Period::of(hours: 1, minutes: 90, seconds: -3540),
            'PT1H31M',
        ];
        yield [
            Period::of(hours: 1, minutes: 90, seconds: -3660),
            'PT1H29M',
        ];
        yield [
            Period::of(hours: -1, minutes: 90, seconds: -3660),
            '-PT31M',
        ];
        yield [
            Period::of(hours: -1, minutes: 61, seconds: -120),
            '-PT1M',
        ];
        yield [
            Period::of(days: 48, hours: -8),
            'P1M17DT16H',
        ];
        yield [
            Period::of(days: 48, hours: -28),
            'P1M16DT20H',
        ];
        yield [
            Period::of(hours: 1, seconds: -3615),
            '-PT15S',
        ];
        yield [
            Period::of(hours: -1, seconds: 3615),
            'PT15S',
        ];
        yield [
            Period::of(hours: 1, seconds: -59),
            'PT59M1S',
        ];
        yield [
            Period::of(hours: -1, seconds: 59),
            '-PT59M1S',
        ];
    }
}
