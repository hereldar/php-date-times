<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use DateInterval as StandardDateInterval;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class PeriodComparisonTest extends TestCase
{
    public function testCompareToNegative(): void
    {
        $first = Period::of(minutes: 1);
        $second = Period::of(minutes: 2);
        self::assertSame(-1, $first->compareTo($second));
    }

    public function testCompareToPositive(): void
    {
        $first = Period::of(days: 1);
        $second = Period::of(hours: 1);
        self::assertSame(1, $first->compareTo($second));
    }

    public function testCompareToZero(): void
    {
        $first = Period::of(years: 1);
        $second = Period::of(years: 1);
        self::assertSame(0, $first->compareTo($second));
    }

    public function testIdenticalToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->is($oneDay));
        self::assertTrue($oneDay->is(Period::of(days: 1)));
    }

    public function testIdenticalToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->is(Period::of(hours: 24)));
        self::assertFalse($oneDay->is(Period::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->is(Period::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->is(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotIdenticalToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isNot(Period::of(hours: 24)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotIdenticalToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isNot($oneDay));
        self::assertFalse($oneDay->isNot(Period::of(days: 1)));
    }

    public function testEqualToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isEqual($oneDay));
        self::assertTrue($oneDay->isEqual(Period::of(days: 1)));
        self::assertTrue($oneDay->isEqual(Period::of(hours: 24)));
        self::assertTrue($oneDay->isEqual(Period::of(hours: 23, minutes: 60)));
    }

    public function testEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isEqual(Period::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->isEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotEqualToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isNotEqual($oneDay));
        self::assertFalse($oneDay->isNotEqual(Period::of(days: 1)));
        self::assertFalse($oneDay->isNotEqual(Period::of(hours: 24)));
        self::assertFalse($oneDay->isNotEqual(Period::of(hours: 23, minutes: 60)));
    }

    public function testGreaterThanToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isGreater(Period::of(days: 1)->minus(microseconds: 1)));
        self::assertTrue($oneDay->isGreater(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testGreaterThanToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isGreater($oneDay));
        self::assertFalse($oneDay->isGreater(Period::of(days: 1)));
        self::assertFalse($oneDay->isGreater(Period::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->isGreater(Period::of(days: 1)->plus(microseconds: 1)));
        self::assertFalse($oneDay->isGreater(Period::of(hours: 23, minutes: 59, seconds: 59, milliseconds: 1001)));
    }

    public function testGreaterThanOrEqualToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isGreaterOrEqual($oneDay));
        self::assertTrue($oneDay->isGreaterOrEqual(Period::of(days: 1)));
        self::assertTrue($oneDay->isGreaterOrEqual(Period::of(hours: 23, minutes: 60)));

        self::assertTrue($oneDay->isGreaterOrEqual(Period::of(days: 1)->minus(microseconds: 1)));
        self::assertTrue($oneDay->isGreaterOrEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testGreaterThanOrEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isGreaterOrEqual(Period::of(days: 1)->plus(microseconds: 1)));
        self::assertFalse($oneDay->isGreaterOrEqual(Period::of(hours: 23, minutes: 59, seconds: 59, milliseconds: 1001)));
    }

    public function testLessThanToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isLess(Period::of(days: 1)->plus(microseconds: 1)));
        self::assertTrue($oneDay->isLess(Period::of(hours: 23, minutes: 59, seconds: 59, milliseconds: 1001)));
    }

    public function testLessThanToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isLess($oneDay));
        self::assertFalse($oneDay->isLess(Period::of(days: 1)));
        self::assertFalse($oneDay->isLess(Period::of(hours: 23, minutes: 60)));

        self::assertFalse($oneDay->isLess(Period::of(days: 1)->minus(microseconds: 1)));
        self::assertFalse($oneDay->isLess(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testLessThanOrEqualToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isLessOrEqual($oneDay));
        self::assertTrue($oneDay->isLessOrEqual(Period::of(days: 1)));
        self::assertTrue($oneDay->isLessOrEqual(Period::of(hours: 23, minutes: 60)));

        self::assertTrue($oneDay->isLessOrEqual(Period::of(days: 1)->plus(microseconds: 1)));
        self::assertTrue($oneDay->isLessOrEqual(Period::of(hours: 23, minutes: 59, seconds: 59, milliseconds: 1001)));
    }

    public function testLessThanOrEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isLessOrEqual(Period::of(days: 1)->minus(microseconds: 1)));
        self::assertFalse($oneDay->isLessOrEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }
}
