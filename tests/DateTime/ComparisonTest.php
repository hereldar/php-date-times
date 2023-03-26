<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Generator;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

/**
 * @internal
 */
final class DateTimeSubclass extends DateTime
{
}

final class ComparisonTest extends TestCase
{
    /**
     * @dataProvider equalDateTimes
     */
    public function testCompareToZero(DateTime $a, DateTime $b): void
    {
        self::assertSame(0, $a->compareTo($b));
        self::assertSame(0, $b->compareTo($a));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, DateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertSame(1, DateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertSame(1, DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, DateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertSame(-1, DateTime::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertSame(-1, DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->compareTo(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testIdenticalToTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->is(DateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testIdenticalToFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->is(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->is(DateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->is(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->is(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->is(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotIdenticalToTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isNot(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNot(DateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isNot(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isNot(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNot(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotIdenticalToFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isNot(DateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    /**
     * @dataProvider equalDateTimes
     */
    public function testEqualToTrue(DateTime $a, DateTime $b): void
    {
        self::assertTrue($a->isEqual($b));
        self::assertTrue($b->isEqual($a));
    }

    public function testEqualToFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testNotEqualToTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isNotEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isNotEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    /**
     * @dataProvider equalDateTimes
     */
    public function testNotEqualToFalse(DateTime $a, DateTime $b): void
    {
        self::assertFalse($a->isNotEqual($b));
        self::assertFalse($b->isNotEqual($a));
    }

    public function testGreaterThanTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testGreaterThanFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreater(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testGreaterThanOrEqualTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testGreaterThanOrEqualTrueEqual(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testGreaterThanOrEqualFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isGreaterOrEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLess(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLess(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLess(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isLess(DateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isLess(DateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLess(DateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public function testLessThanOrEqualTrue(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 1)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 12, 0, 1)));
    }

    public function testLessThanOrEqualTrueEqual(): void
    {
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)));
        self::assertTrue(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 12, 0, 0)));
    }

    public function testLessThanOrEqualFalse(): void
    {
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(DateTime::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTimeSubclass::of(2000, 1, 1, 11, 59, 59)));
        self::assertFalse(DateTimeSubclass::of(2000, 1, 1, 12, 0, 0)->isLessOrEqual(DateTime::of(2000, 1, 1, 11, 59, 59)));
    }

    public static function equalDateTimes(): Generator
    {
        yield [
            DateTime::of(2000, 1, 1, 12, 0, 0),
            DateTime::of(2000, 1, 1, 12, 0, 0),
        ];
        yield [
            DateTime::of(2000, 1, 1, 12, 0, 0),
            DateTimeSubclass::of(2000, 1, 1, 12, 0, 0),
        ];
        yield [
            DateTimeSubclass::of(2000, 1, 1, 12, 0, 0),
            DateTimeSubclass::of(2000, 1, 1, 12, 0, 0),
        ];
        yield [
            DateTime::of(2010, 4, 24, 10, 24, 16, 0, '-04:00'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 0, '-04:00'),
        ];
        yield [
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, Offset::of(2, 30)),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, Offset::of(2, 30)),
        ];
        yield [
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, TimeZone::of('Europe/Madrid')),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, TimeZone::of('Europe/Madrid')),
        ];
    }
}
