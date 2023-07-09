<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testHour(): void
    {
        $time = LocalTime::of(12, 30, 25, 999);
        self::assertSame(12, $time->hour());
    }

    public function testMinute(): void
    {
        $time = LocalTime::of(12, 30, 25, 999);
        self::assertSame(30, $time->minute());
    }

    public function testSecond(): void
    {
        $time = LocalTime::of(12, 30, 25, 999);
        self::assertSame(25, $time->second());
    }

    public function testMillisecond(): void
    {
        $time = LocalTime::of(12, 30, 25, 999);
        self::assertSame(0, $time->millisecond());

        $time = LocalTime::of(12, 30, 25, 999_999);
        self::assertSame(999, $time->millisecond());
    }

    public function testMicrosecond(): void
    {
        $time = LocalTime::of(12, 30, 25, 999);
        self::assertSame(999, $time->microsecond());
    }
}
