<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

/** @internal */
final class LocalTimeSubclass extends LocalTime {}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, LocalTime::of(12, 0, 0)->compareTo(LocalTime::of(12, 0, 0)));
        self::assertSame(0, LocalTime::of(12, 0, 0)->compareTo(LocalTimeSubclass::of(12, 0, 0)));
        self::assertSame(0, LocalTimeSubclass::of(12, 0, 0)->compareTo(LocalTime::of(12, 0, 0)));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, LocalTime::of(12, 0, 0)->compareTo(LocalTime::of(11, 59, 59)));
        self::assertSame(1, LocalTime::of(12, 0, 0)->compareTo(LocalTimeSubclass::of(11, 59, 59)));
        self::assertSame(1, LocalTimeSubclass::of(12, 0, 0)->compareTo(LocalTime::of(11, 59, 59)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, LocalTime::of(12, 0, 0)->compareTo(LocalTime::of(12, 0, 1)));
        self::assertSame(-1, LocalTime::of(12, 0, 0)->compareTo(LocalTimeSubclass::of(12, 0, 1)));
        self::assertSame(-1, LocalTimeSubclass::of(12, 0, 0)->compareTo(LocalTime::of(12, 0, 1)));
    }

    public function testIdenticalToTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->is(LocalTime::of(12, 0, 0)));
    }

    public function testIdenticalToFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->is(LocalTimeSubclass::of(12, 0, 0)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->is(LocalTime::of(12, 0, 0)));
        self::assertFalse(LocalTime::of(12, 0, 0)->is(LocalTime::of(12, 0, 1)));
        self::assertFalse(LocalTime::of(12, 0, 0)->is(LocalTimeSubclass::of(12, 0, 1)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->is(LocalTime::of(12, 0, 1)));
    }

    public function testNotIdenticalToTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isNot(LocalTimeSubclass::of(12, 0, 0)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isNot(LocalTime::of(12, 0, 0)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isNot(LocalTime::of(12, 0, 1)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isNot(LocalTimeSubclass::of(12, 0, 1)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isNot(LocalTime::of(12, 0, 1)));
    }

    public function testNotIdenticalToFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isNot(LocalTime::of(12, 0, 0)));
    }

    public function testEqualToTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isEqual(LocalTime::of(12, 0, 0)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isEqual(LocalTimeSubclass::of(12, 0, 0)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isEqual(LocalTime::of(12, 0, 0)));
    }

    public function testEqualToFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isEqual(LocalTime::of(12, 0, 1)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isEqual(LocalTimeSubclass::of(12, 0, 1)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isEqual(LocalTime::of(12, 0, 1)));
    }

    public function testNotEqualToTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isNotEqual(LocalTime::of(12, 0, 1)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isNotEqual(LocalTimeSubclass::of(12, 0, 1)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isNotEqual(LocalTime::of(12, 0, 1)));
    }

    public function testNotEqualToFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isNotEqual(LocalTime::of(12, 0, 0)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isNotEqual(LocalTimeSubclass::of(12, 0, 0)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isNotEqual(LocalTime::of(12, 0, 0)));
    }

    public function testGreaterThanTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreater(LocalTime::of(11, 59, 59)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreater(LocalTimeSubclass::of(11, 59, 59)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isGreater(LocalTime::of(11, 59, 59)));
    }

    public function testGreaterThanFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isGreater(LocalTime::of(12, 0, 1)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isGreater(LocalTimeSubclass::of(12, 0, 1)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isGreater(LocalTime::of(12, 0, 1)));
    }

    public function testGreaterThanOrEqualTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(11, 59, 59)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTimeSubclass::of(11, 59, 59)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(11, 59, 59)));
    }

    public function testGreaterThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(12, 0, 0)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTimeSubclass::of(12, 0, 0)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(12, 0, 0)));
    }

    public function testGreaterThanOrEqualFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(12, 0, 1)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isGreaterOrEqual(LocalTimeSubclass::of(12, 0, 1)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isGreaterOrEqual(LocalTime::of(12, 0, 1)));
    }

    public function testLessThanTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isLess(LocalTime::of(12, 0, 1)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isLess(LocalTimeSubclass::of(12, 0, 1)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isLess(LocalTime::of(12, 0, 1)));
    }

    public function testLessThanFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isLess(LocalTime::of(11, 59, 59)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isLess(LocalTimeSubclass::of(11, 59, 59)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isLess(LocalTime::of(11, 59, 59)));
    }

    public function testLessThanOrEqualTrue(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTime::of(12, 0, 1)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTimeSubclass::of(12, 0, 1)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isLessOrEqual(LocalTime::of(12, 0, 1)));
    }

    public function testLessThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTime::of(12, 0, 0)));
        self::assertTrue(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTimeSubclass::of(12, 0, 0)));
        self::assertTrue(LocalTimeSubclass::of(12, 0, 0)->isLessOrEqual(LocalTime::of(12, 0, 0)));
    }

    public function testLessThanOrEqualFalse(): void
    {
        self::assertFalse(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTime::of(11, 59, 59)));
        self::assertFalse(LocalTime::of(12, 0, 0)->isLessOrEqual(LocalTimeSubclass::of(11, 59, 59)));
        self::assertFalse(LocalTimeSubclass::of(12, 0, 0)->isLessOrEqual(LocalTime::of(11, 59, 59)));
    }
}
