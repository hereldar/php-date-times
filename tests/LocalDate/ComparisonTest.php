<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

/** @internal */
final class LocalDateSubclass extends LocalDate {}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, LocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(2000, 1, 1)));
        self::assertSame(0, LocalDate::of(2000, 1, 1)->compareTo(CustomLocalDate::of(2000, 1, 1)));
        self::assertSame(0, CustomLocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(2000, 1, 1)));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, LocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(1999, 12, 31)));
        self::assertSame(1, LocalDate::of(2000, 1, 1)->compareTo(CustomLocalDate::of(1999, 12, 31)));
        self::assertSame(1, CustomLocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(1999, 12, 31)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, LocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(2000, 1, 2)));
        self::assertSame(-1, LocalDate::of(2000, 1, 1)->compareTo(CustomLocalDate::of(2000, 1, 2)));
        self::assertSame(-1, CustomLocalDate::of(2000, 1, 1)->compareTo(LocalDate::of(2000, 1, 2)));
    }

    public function testIdenticalToTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->is(LocalDate::of(2000, 1, 1)));
    }

    public function testIdenticalToFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->is(CustomLocalDate::of(2000, 1, 1)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->is(LocalDate::of(2000, 1, 1)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->is(LocalDate::of(2000, 1, 2)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->is(CustomLocalDate::of(2000, 1, 2)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->is(LocalDate::of(2000, 1, 2)));
    }

    public function testNotIdenticalToTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isNot(CustomLocalDate::of(2000, 1, 1)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isNot(LocalDate::of(2000, 1, 1)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isNot(LocalDate::of(2000, 1, 2)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isNot(CustomLocalDate::of(2000, 1, 2)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isNot(LocalDate::of(2000, 1, 2)));
    }

    public function testNotIdenticalToFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isNot(LocalDate::of(2000, 1, 1)));
    }

    public function testEqualToTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isEqual(LocalDate::of(2000, 1, 1)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isEqual(CustomLocalDate::of(2000, 1, 1)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isEqual(LocalDate::of(2000, 1, 1)));
    }

    public function testEqualToFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isEqual(LocalDate::of(2000, 1, 2)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isEqual(CustomLocalDate::of(2000, 1, 2)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isEqual(LocalDate::of(2000, 1, 2)));
    }

    public function testNotEqualToTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isNotEqual(LocalDate::of(2000, 1, 2)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isNotEqual(CustomLocalDate::of(2000, 1, 2)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isNotEqual(LocalDate::of(2000, 1, 2)));
    }

    public function testNotEqualToFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isNotEqual(LocalDate::of(2000, 1, 1)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isNotEqual(CustomLocalDate::of(2000, 1, 1)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isNotEqual(LocalDate::of(2000, 1, 1)));
    }

    public function testGreaterThanTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreater(LocalDate::of(1999, 12, 31)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreater(CustomLocalDate::of(1999, 12, 31)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isGreater(LocalDate::of(1999, 12, 31)));
    }

    public function testGreaterThanFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isGreater(LocalDate::of(2000, 1, 2)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isGreater(CustomLocalDate::of(2000, 1, 2)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isGreater(LocalDate::of(2000, 1, 2)));
    }

    public function testGreaterThanOrEqualTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(1999, 12, 31)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(CustomLocalDate::of(1999, 12, 31)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(1999, 12, 31)));
    }

    public function testGreaterThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(2000, 1, 1)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(CustomLocalDate::of(2000, 1, 1)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(2000, 1, 1)));
    }

    public function testGreaterThanOrEqualFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(2000, 1, 2)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isGreaterOrEqual(CustomLocalDate::of(2000, 1, 2)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isGreaterOrEqual(LocalDate::of(2000, 1, 2)));
    }

    public function testLessThanTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLess(LocalDate::of(2000, 1, 2)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLess(CustomLocalDate::of(2000, 1, 2)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isLess(LocalDate::of(2000, 1, 2)));
    }

    public function testLessThanFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isLess(LocalDate::of(1999, 12, 31)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isLess(CustomLocalDate::of(1999, 12, 31)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isLess(LocalDate::of(1999, 12, 31)));
    }

    public function testLessThanOrEqualTrue(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(2000, 1, 2)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLessOrEqual(CustomLocalDate::of(2000, 1, 2)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(2000, 1, 2)));
    }

    public function testLessThanOrEqualTrueEqual(): void
    {
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(2000, 1, 1)));
        self::assertTrue(LocalDate::of(2000, 1, 1)->isLessOrEqual(CustomLocalDate::of(2000, 1, 1)));
        self::assertTrue(CustomLocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(2000, 1, 1)));
    }

    public function testLessThanOrEqualFalse(): void
    {
        self::assertFalse(LocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(1999, 12, 31)));
        self::assertFalse(LocalDate::of(2000, 1, 1)->isLessOrEqual(CustomLocalDate::of(1999, 12, 31)));
        self::assertFalse(CustomLocalDate::of(2000, 1, 1)->isLessOrEqual(LocalDate::of(1999, 12, 31)));
    }
}
