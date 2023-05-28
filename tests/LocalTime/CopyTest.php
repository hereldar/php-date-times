<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testHour(): void
    {
        $one = LocalTime::of(hour: 10);
        $two = $one->with(hour: 3);
        self::assertLocalTime($one, 10, 0, 0, 0);
        self::assertLocalTime($two, 3, 0, 0, 0);
    }

    public function testMinute(): void
    {
        $one = LocalTime::of(minute: 10);
        $two = $one->with(minute: 3);
        self::assertLocalTime($one, 0, 10, 0, 0);
        self::assertLocalTime($two, 0, 3, 0, 0);
    }

    public function testSecond(): void
    {
        $one = LocalTime::of(second: 10);
        $two = $one->with(second: 3);
        self::assertLocalTime($one, 0, 0, 10, 0);
        self::assertLocalTime($two, 0, 0, 3, 0);
    }

    public function testMicrosecond(): void
    {
        $one = LocalTime::of(microsecond: 10);
        $two = $one->with(microsecond: 3);
        self::assertLocalTime($one, 0, 0, 0, 10);
        self::assertLocalTime($two, 0, 0, 0, 3);
    }

    public function testAll(): void
    {
        $one = LocalTime::parse('10:10:10.000010', 'H:i:s.u')->orFail();
        $two = $one->with(3, 3, 3, 3);
        self::assertLocalTime($one, 10, 10, 10, 10);
        self::assertLocalTime($two, 3, 3, 3, 3);
    }
}
