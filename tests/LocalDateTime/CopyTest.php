<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = LocalDateTime::of(year: 10);
        $two = $one->with(year: 3);
        self::assertLocalDateTime($one, 10, 1, 1, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 3, 1, 1, 0, 0, 0, 0);
    }

    public function testMonth(): void
    {
        $one = LocalDateTime::of(month: 10);
        $two = $one->with(month: 3);
        self::assertLocalDateTime($one, 1970, 10, 1, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 3, 1, 0, 0, 0, 0);
    }

    public function testDay(): void
    {
        $one = LocalDateTime::of(day: 10);
        $two = $one->with(day: 3);
        self::assertLocalDateTime($one, 1970, 1, 10, 0, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 3, 0, 0, 0, 0);
    }

    public function testHour(): void
    {
        $one = LocalDateTime::of(hour: 10);
        $two = $one->with(hour: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 10, 0, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 3, 0, 0, 0);
    }

    public function testMinute(): void
    {
        $one = LocalDateTime::of(minute: 10);
        $two = $one->with(minute: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 10, 0, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 3, 0, 0);
    }

    public function testSecond(): void
    {
        $one = LocalDateTime::of(second: 10);
        $two = $one->with(second: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 0, 10, 0);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 0, 3, 0);
    }

    public function testMicrosecond(): void
    {
        $one = LocalDateTime::of(microsecond: 10);
        $two = $one->with(microsecond: 3);
        self::assertLocalDateTime($one, 1970, 1, 1, 0, 0, 0, 10);
        self::assertLocalDateTime($two, 1970, 1, 1, 0, 0, 0, 3);
    }

    public function testAll(): void
    {
        $one = LocalDateTime::parse('0010-10-10 10:10:10.000010', 'Y-m-d H:i:s.u')->orFail();
        $two = $one->with(3, 3, 3, 3, 3, 3, 3);
        self::assertLocalDateTime($one, 10, 10, 10, 10, 10, 10, 10);
        self::assertLocalDateTime($two, 3, 3, 3, 3, 3, 3, 3);
    }
}
