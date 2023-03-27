<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

/**
 * @internal
 */
final class PeriodSubclass extends Period
{
}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, Period::of(years: 1)->compareTo(Period::of(years: 1)));
        self::assertSame(0, Period::of(months: 2)->compareTo(Period::of(months: 2)));
        self::assertSame(0, Period::of(weeks: 3)->compareTo(Period::of(weeks: 3)));
        self::assertSame(0, Period::of(weeks: 3)->compareTo(Period::of(days: 21)));
        self::assertSame(0, Period::of(days: 4)->compareTo(Period::of(days: 4)));
        self::assertSame(0, Period::of(hours: 5)->compareTo(Period::of(hours: 5)));
        self::assertSame(0, Period::of(minutes: 6)->compareTo(Period::of(minutes: 6)));
        self::assertSame(0, Period::of(seconds: 7)->compareTo(Period::of(seconds: 7)));
        self::assertSame(0, Period::of(milliseconds: 8)->compareTo(Period::of(milliseconds: 8)));
        self::assertSame(0, Period::of(milliseconds: 8)->compareTo(Period::of(microseconds: 8_000)));
        self::assertSame(0, Period::of(microseconds: 9)->compareTo(Period::of(microseconds: 9)));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, Period::of(years: 1)->compareTo(Period::of(years: 0)));
        self::assertSame(1, Period::of(months: 2)->compareTo(Period::of(months: 1)));
        self::assertSame(1, Period::of(weeks: 3)->compareTo(Period::of(weeks: 2)));
        self::assertSame(1, Period::of(weeks: 3)->compareTo(Period::of(days: 14)));
        self::assertSame(1, Period::of(days: 4)->compareTo(Period::of(days: 3)));
        self::assertSame(1, Period::of(hours: 5)->compareTo(Period::of(hours: 4)));
        self::assertSame(1, Period::of(minutes: 6)->compareTo(Period::of(minutes: 5)));
        self::assertSame(1, Period::of(seconds: 7)->compareTo(Period::of(seconds: 6)));
        self::assertSame(1, Period::of(milliseconds: 8)->compareTo(Period::of(milliseconds: 7)));
        self::assertSame(1, Period::of(milliseconds: 8)->compareTo(Period::of(microseconds: 7_999)));
        self::assertSame(1, Period::of(microseconds: 9)->compareTo(Period::of(microseconds: 8)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, Period::of(years: 1)->compareTo(Period::of(years: 2)));
        self::assertSame(-1, Period::of(months: 2)->compareTo(Period::of(months: 3)));
        self::assertSame(-1, Period::of(weeks: 3)->compareTo(Period::of(weeks: 4)));
        self::assertSame(-1, Period::of(weeks: 3)->compareTo(Period::of(days: 28)));
        self::assertSame(-1, Period::of(days: 4)->compareTo(Period::of(days: 5)));
        self::assertSame(-1, Period::of(hours: 5)->compareTo(Period::of(hours: 6)));
        self::assertSame(-1, Period::of(minutes: 6)->compareTo(Period::of(minutes: 7)));
        self::assertSame(-1, Period::of(seconds: 7)->compareTo(Period::of(seconds: 8)));
        self::assertSame(-1, Period::of(milliseconds: 8)->compareTo(Period::of(milliseconds: 9)));
        self::assertSame(-1, Period::of(milliseconds: 8)->compareTo(Period::of(microseconds: 8_001)));
        self::assertSame(-1, Period::of(microseconds: 9)->compareTo(Period::of(microseconds: 10)));
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
        self::assertFalse($oneDay->is(PeriodSubclass::of(days: 1)));
        self::assertFalse($oneDay->is(PeriodSubclass::of(hours: 24)));
        self::assertFalse($oneDay->is(PeriodSubclass::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->is(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->is(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotIdenticalToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isNot(Period::of(hours: 24)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNot(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
        self::assertTrue($oneDay->isNot(PeriodSubclass::of(days: 1)));
        self::assertTrue($oneDay->isNot(PeriodSubclass::of(hours: 24)));
        self::assertTrue($oneDay->isNot(PeriodSubclass::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isNot(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNot(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
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
        self::assertTrue($oneDay->isEqual(PeriodSubclass::of(days: 1)));
    }

    public function testEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isEqual(Period::of(hours: 24)));
        self::assertFalse($oneDay->isEqual(Period::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->isEqual(Period::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->isEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
        self::assertFalse($oneDay->isEqual(PeriodSubclass::of(hours: 24)));
        self::assertFalse($oneDay->isEqual(PeriodSubclass::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->isEqual(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->isEqual(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotEqualToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 24)));
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNotEqual(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
        self::assertTrue($oneDay->isNotEqual(PeriodSubclass::of(hours: 24)));
        self::assertTrue($oneDay->isNotEqual(PeriodSubclass::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isNotEqual(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNotEqual(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotEqualToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isNotEqual($oneDay));
        self::assertFalse($oneDay->isNotEqual(Period::of(days: 1)));
        self::assertFalse($oneDay->isNotEqual(PeriodSubclass::of(days: 1)));
    }

    public function testSimilarToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isSimilar($oneDay));
        self::assertTrue($oneDay->isSimilar(Period::of(days: 1)));
        self::assertTrue($oneDay->isSimilar(Period::of(hours: 24)));
        self::assertTrue($oneDay->isSimilar(Period::of(hours: 23, minutes: 60)));
        self::assertTrue($oneDay->isSimilar(PeriodSubclass::of(days: 1)));
        self::assertTrue($oneDay->isSimilar(PeriodSubclass::of(hours: 24)));
        self::assertTrue($oneDay->isSimilar(PeriodSubclass::of(hours: 23, minutes: 60)));
    }

    public function testSimilarToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isSimilar(Period::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->isSimilar(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
        self::assertFalse($oneDay->isSimilar(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertFalse($oneDay->isSimilar(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotSimilarToTrue(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertTrue($oneDay->isNotSimilar(Period::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNotSimilar(Period::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
        self::assertTrue($oneDay->isNotSimilar(PeriodSubclass::of(hours: 24, microseconds: 1)));
        self::assertTrue($oneDay->isNotSimilar(PeriodSubclass::of(hours: 23, minutes: 59, seconds: 59, microseconds: 999999)));
    }

    public function testNotSimilarToFalse(): void
    {
        $oneDay = Period::of(days: 1);
        self::assertFalse($oneDay->isNotSimilar($oneDay));
        self::assertFalse($oneDay->isNotSimilar(Period::of(days: 1)));
        self::assertFalse($oneDay->isNotSimilar(Period::of(hours: 24)));
        self::assertFalse($oneDay->isNotSimilar(Period::of(hours: 23, minutes: 60)));
        self::assertFalse($oneDay->isNotSimilar(PeriodSubclass::of(days: 1)));
        self::assertFalse($oneDay->isNotSimilar(PeriodSubclass::of(hours: 24)));
        self::assertFalse($oneDay->isNotSimilar(PeriodSubclass::of(hours: 23, minutes: 60)));
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
