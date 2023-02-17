<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

/**
 * @internal
 */
final class LocalDateTimeSubclass extends LocalDateTime
{
}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertSame(0, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertSame(0, LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertSame(1, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertSame(1, LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertSame(-1, LocalDateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertSame(-1, LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->compareTo(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testIdenticalToTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testIdenticalToFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->is(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotIdenticalToTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotIdenticalToFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNot(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testEqualToTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testEqualToFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotEqualToTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotEqualToFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNotEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testGreaterThanTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testGreaterThanFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreater(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testGreaterThanOrEqualTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testGreaterThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testGreaterThanOrEqualFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLess(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testLessThanOrEqualTrue(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testLessThanOrEqualFalse(): void
    {
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(LocalDateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(LocalDateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(LocalDateTime::of(2000, 1, 1, 11, 59, 59)));
    }
}
