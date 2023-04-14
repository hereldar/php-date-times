<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use Hereldar\DateTimes\TimeZone;
use Hereldar\DateTimes\Tests\TestCase;

/**
 * @internal
 */
final class TimeZoneSubclass extends TimeZone
{
}

final class ComparisonTest extends TestCase
{
    public function testCompareToZero(): void
    {
        self::assertSame(0, TimeZone::utc()->compareTo(TimeZone::of('UTC')));
        self::assertSame(0, TimeZone::of('CEST')->compareTo(TimeZone::of('CEST')));
        self::assertSame(0, TimeZone::of('Europe/Madrid')->compareTo(TimeZone::of('Europe/Madrid')));
    }

    public function testCompareToPositive(): void
    {
        self::assertSame(1, TimeZone::utc()->compareTo(TimeZone::of('-05:00')));
        self::assertSame(1, TimeZone::utc()->compareTo(TimeZone::of('GMT')));
        self::assertSame(1, TimeZone::of('CEST')->compareTo(TimeZone::of('PDT')));
        self::assertSame(1, TimeZone::of('Europe/Madrid')->compareTo(TimeZone::of('America/New_York')));
    }

    public function testCompareToNegative(): void
    {
        self::assertSame(-1, TimeZone::utc()->compareTo(TimeZone::of('+03:00')));
        self::assertSame(-1, TimeZone::of('GMT')->compareTo(TimeZone::utc()));
        self::assertSame(-1, TimeZone::of('CEST')->compareTo(TimeZone::of('EEST')));
        self::assertSame(-1, TimeZone::of('Europe/Madrid')->compareTo(TimeZone::of('Asia/Dubai')));
    }

    public function testIdenticalToTrue(): void
    {
        $utc = TimeZone::utc();
        self::assertTrue($utc->is($utc));
        self::assertTrue($utc->is(TimeZone::utc()));
        self::assertTrue($utc->is(TimeZone::of('UTC')));
    }

    public function testIdenticalToFalse(): void
    {
        $utc = TimeZone::utc();
        self::assertFalse($utc->is(TimeZone::of('GMT')));
        self::assertFalse($utc->is(TimeZoneSubclass::utc()));
        self::assertFalse($utc->is(TimeZoneSubclass::of('UTC')));
        self::assertFalse($utc->is(TimeZoneSubclass::of('GMT')));
    }

    public function testNotIdenticalToTrue(): void
    {
        $utc = TimeZone::utc();
        self::assertTrue($utc->isNot(TimeZone::of('GMT')));
        self::assertTrue($utc->isNot(TimeZoneSubclass::utc()));
        self::assertTrue($utc->isNot(TimeZoneSubclass::of('UTC')));
        self::assertTrue($utc->isNot(TimeZoneSubclass::of('GMT')));
    }

    public function testNotIdenticalToFalse(): void
    {
        $utc = TimeZone::utc();
        self::assertFalse($utc->isNot($utc));
        self::assertFalse($utc->isNot(TimeZone::utc()));
        self::assertFalse($utc->isNot(TimeZone::of('UTC')));
    }

    public function testEqualToTrue(): void
    {
        $utc = TimeZone::utc();
        self::assertTrue($utc->isEqual($utc));
        self::assertTrue($utc->isEqual(TimeZone::utc()));
        self::assertTrue($utc->isEqual(TimeZone::of('UTC')));
        self::assertTrue($utc->isEqual(TimeZoneSubclass::utc()));
        self::assertTrue($utc->isEqual(TimeZoneSubclass::of('UTC')));
    }

    public function testEqualToFalse(): void
    {
        $utc = TimeZone::utc();
        self::assertFalse($utc->isEqual(TimeZone::of('GMT')));
        self::assertFalse($utc->isEqual(TimeZoneSubclass::of('GMT')));
    }

    public function testNotEqualToTrue(): void
    {
        $utc = TimeZone::utc();
        self::assertTrue($utc->isNotEqual(TimeZone::of('GMT')));
        self::assertTrue($utc->isNotEqual(TimeZoneSubclass::of('GMT')));
    }

    public function testNotEqualToFalse(): void
    {
        $utc = TimeZone::utc();
        self::assertFalse($utc->isNotEqual($utc));
        self::assertFalse($utc->isNotEqual(TimeZone::utc()));
        self::assertFalse($utc->isNotEqual(TimeZone::of('UTC')));
        self::assertFalse($utc->isNotEqual(TimeZoneSubclass::utc()));
        self::assertFalse($utc->isNotEqual(TimeZoneSubclass::of('UTC')));
    }
}
