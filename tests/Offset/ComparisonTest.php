<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

/** @internal */
final class OffsetSubclass extends Offset {}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, Offset::of(5)->compareTo(Offset::of(5)));
        self::assertSame(0, Offset::of(0, 6)->compareTo(Offset::of(0, 6)));
        self::assertSame(0, Offset::of(0, 0, 7)->compareTo(Offset::of(0, 0, 7)));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, Offset::of(5)->compareTo(Offset::of(4)));
        self::assertSame(1, Offset::of(0, 6)->compareTo(Offset::of(0, 5)));
        self::assertSame(1, Offset::of(0, 0, 7)->compareTo(Offset::of(0, 0, 6)));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, Offset::of(5)->compareTo(Offset::of(6)));
        self::assertSame(-1, Offset::of(0, 6)->compareTo(Offset::of(0, 7)));
        self::assertSame(-1, Offset::of(0, 0, 7)->compareTo(Offset::of(0, 0, 8)));
    }

    public function testIdenticalToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->is($oneHour));
        self::assertTrue($oneHour->is(Offset::of(1)));
        self::assertTrue($oneHour->is(Offset::fromTotalMinutes(60)));
        self::assertTrue($oneHour->is(Offset::fromTotalSeconds(3600)));
    }

    public function testIdenticalToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->is(Offset::fromTotalSeconds(3601)));
        self::assertFalse($oneHour->is(Offset::fromTotalSeconds(3599)));
        self::assertFalse($oneHour->is(OffsetSubclass::of(1)));
        self::assertFalse($oneHour->is(OffsetSubclass::fromTotalMinutes(60)));
        self::assertFalse($oneHour->is(OffsetSubclass::fromTotalSeconds(3600)));
        self::assertFalse($oneHour->is(OffsetSubclass::fromTotalSeconds(3601)));
        self::assertFalse($oneHour->is(OffsetSubclass::fromTotalSeconds(3599)));
    }

    public function testNotIdenticalToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isNot(Offset::fromTotalSeconds(3601)));
        self::assertTrue($oneHour->isNot(Offset::fromTotalSeconds(3599)));
        self::assertTrue($oneHour->isNot(OffsetSubclass::of(1)));
        self::assertTrue($oneHour->isNot(OffsetSubclass::fromTotalMinutes(60)));
        self::assertTrue($oneHour->isNot(OffsetSubclass::fromTotalSeconds(3600)));
        self::assertTrue($oneHour->isNot(OffsetSubclass::fromTotalSeconds(3601)));
        self::assertTrue($oneHour->isNot(OffsetSubclass::fromTotalSeconds(3599)));
    }

    public function testNotIdenticalToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isNot($oneHour));
        self::assertFalse($oneHour->isNot(Offset::of(1)));
        self::assertFalse($oneHour->isNot(Offset::fromTotalMinutes(60)));
        self::assertFalse($oneHour->isNot(Offset::fromTotalSeconds(3600)));
    }

    public function testEqualToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isEqual($oneHour));
        self::assertTrue($oneHour->isEqual(Offset::of(1)));
        self::assertTrue($oneHour->isEqual(Offset::fromTotalMinutes(60)));
        self::assertTrue($oneHour->isEqual(Offset::fromTotalSeconds(3600)));
        self::assertTrue($oneHour->isEqual(OffsetSubclass::of(1)));
        self::assertTrue($oneHour->isEqual(OffsetSubclass::fromTotalMinutes(60)));
        self::assertTrue($oneHour->isEqual(OffsetSubclass::fromTotalSeconds(3600)));
    }

    public function testEqualToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isEqual(Offset::fromTotalSeconds(3601)));
        self::assertFalse($oneHour->isEqual(Offset::fromTotalSeconds(3599)));
        self::assertFalse($oneHour->isEqual(OffsetSubclass::fromTotalSeconds(3601)));
        self::assertFalse($oneHour->isEqual(OffsetSubclass::fromTotalSeconds(3599)));
    }

    public function testNotEqualToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isNotEqual(Offset::fromTotalSeconds(3601)));
        self::assertTrue($oneHour->isNotEqual(Offset::fromTotalSeconds(3599)));
        self::assertTrue($oneHour->isNotEqual(OffsetSubclass::fromTotalSeconds(3601)));
        self::assertTrue($oneHour->isNotEqual(OffsetSubclass::fromTotalSeconds(3599)));
    }

    public function testNotEqualToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isNotEqual($oneHour));
        self::assertFalse($oneHour->isNotEqual(Offset::of(1)));
        self::assertFalse($oneHour->isNotEqual(Offset::fromTotalMinutes(60)));
        self::assertFalse($oneHour->isNotEqual(Offset::fromTotalSeconds(3600)));
        self::assertFalse($oneHour->isNotEqual(OffsetSubclass::of(1)));
        self::assertFalse($oneHour->isNotEqual(OffsetSubclass::fromTotalMinutes(60)));
        self::assertFalse($oneHour->isNotEqual(OffsetSubclass::fromTotalSeconds(3600)));
    }

    public function testGreaterThanToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isGreater(Offset::of(1)->minus(seconds: 1)));
        self::assertTrue($oneHour->isGreater(Offset::fromTotalSeconds(3599)));
    }

    public function testGreaterThanToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isGreater($oneHour));
        self::assertFalse($oneHour->isGreater(Offset::of(1)));
        self::assertFalse($oneHour->isGreater(Offset::fromTotalSeconds(3600)));
        self::assertFalse($oneHour->isGreater(Offset::of(1)->plus(seconds: 1)));
    }

    public function testGreaterThanOrEqualToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isGreaterOrEqual($oneHour));
        self::assertTrue($oneHour->isGreaterOrEqual(Offset::of(1)));
        self::assertTrue($oneHour->isGreaterOrEqual(Offset::fromTotalSeconds(3600)));

        self::assertTrue($oneHour->isGreaterOrEqual(Offset::of(1)->minus(seconds: 1)));
        self::assertTrue($oneHour->isGreaterOrEqual(Offset::fromTotalSeconds(3599)));
    }

    public function testGreaterThanOrEqualToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isGreaterOrEqual(Offset::of(1)->plus(seconds: 1)));
    }

    public function testLessThanToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isLess(Offset::of(1)->plus(seconds: 1)));
    }

    public function testLessThanToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isLess($oneHour));
        self::assertFalse($oneHour->isLess(Offset::of(1)));
        self::assertFalse($oneHour->isLess(Offset::fromTotalSeconds(3600)));

        self::assertFalse($oneHour->isLess(Offset::of(1)->minus(seconds: 1)));
        self::assertFalse($oneHour->isLess(Offset::fromTotalSeconds(3599)));
    }

    public function testLessThanOrEqualToTrue(): void
    {
        $oneHour = Offset::of(1);
        self::assertTrue($oneHour->isLessOrEqual($oneHour));
        self::assertTrue($oneHour->isLessOrEqual(Offset::of(1)));
        self::assertTrue($oneHour->isLessOrEqual(Offset::fromTotalSeconds(3600)));

        self::assertTrue($oneHour->isLessOrEqual(Offset::of(1)->plus(seconds: 1)));
    }

    public function testLessThanOrEqualToFalse(): void
    {
        $oneHour = Offset::of(1);
        self::assertFalse($oneHour->isLessOrEqual(Offset::of(1)->minus(seconds: 1)));
        self::assertFalse($oneHour->isLessOrEqual(Offset::fromTotalSeconds(3599)));
    }
}
