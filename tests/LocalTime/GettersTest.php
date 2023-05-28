<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testHour(): void
    {
        $dateTime = LocalTime::of(12, 30, 25, 999);
        self::assertSame(12, $dateTime->hour());
    }

    public function testMinute(): void
    {
        $dateTime = LocalTime::of(12, 30, 25, 999);
        self::assertSame(30, $dateTime->minute());
    }

    public function testSecond(): void
    {
        $dateTime = LocalTime::of(12, 30, 25, 999);
        self::assertSame(25, $dateTime->second());
    }

    public function testMillisecond(): void
    {
        $dateTime = LocalTime::of(12, 30, 25, 999);
        self::assertSame(0, $dateTime->millisecond());

        $dateTime = LocalTime::of(12, 30, 25, 999_999);
        self::assertSame(999, $dateTime->millisecond());
    }

    public function testMicrosecond(): void
    {
        $dateTime = LocalTime::of(12, 30, 25, 999);
        self::assertSame(999, $dateTime->microsecond());
    }
}
