<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class ConversionTest extends TestCase
{
    public function testTimeZone(): void
    {
        self::assertTimeZone(Offset::of(3)->toTimeZone(), '+03:00');
        self::assertTimeZone(Offset::of(5, 15)->toTimeZone(), '+05:15');
        self::assertTimeZone(Offset::fromTotalMinutes(150)->toTimeZone(), '+02:30');
        self::assertTimeZone(Offset::fromTotalSeconds(31_500)->toTimeZone(), '+08:45');

        self::assertTimeZone(Offset::of(-3)->toTimeZone(), '-03:00');
        self::assertTimeZone(Offset::of(-5, -15)->toTimeZone(), '-05:15');
        self::assertTimeZone(Offset::fromTotalMinutes(-150)->toTimeZone(), '-02:30');
        self::assertTimeZone(Offset::fromTotalSeconds(-31_500)->toTimeZone(), '-08:45');
    }
}
