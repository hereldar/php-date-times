<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = DateTime::of(year: 10);
        $two = $one->with(year: 3);
        self::assertDateTime($one, 10, 1, 1, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 3, 1, 1, 0, 0, 0, 0, 'UTC');
    }

    public function testMonth(): void
    {
        $one = DateTime::of(month: 10);
        $two = $one->with(month: 3);
        self::assertDateTime($one, 1970, 10, 1, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 3, 1, 0, 0, 0, 0, 'UTC');
    }

    public function testDay(): void
    {
        $one = DateTime::of(day: 10);
        $two = $one->with(day: 3);
        self::assertDateTime($one, 1970, 1, 10, 0, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 3, 0, 0, 0, 0, 'UTC');
    }

    public function testHour(): void
    {
        $one = DateTime::of(hour: 10);
        $two = $one->with(hour: 3);
        self::assertDateTime($one, 1970, 1, 1, 10, 0, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 3, 0, 0, 0, 'UTC');
    }

    public function testMinute(): void
    {
        $one = DateTime::of(minute: 10);
        $two = $one->with(minute: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 10, 0, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 3, 0, 0, 'UTC');
    }

    public function testSecond(): void
    {
        $one = DateTime::of(second: 10);
        $two = $one->with(second: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 10, 0, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 3, 0, 'UTC');
    }

    public function testMicrosecond(): void
    {
        $one = DateTime::of(microsecond: 10);
        $two = $one->with(microsecond: 3);
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 10, 'UTC');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 3, 'UTC');
    }

    public function testTimeZone(): void
    {
        $one = DateTime::of(timeZone: 'EST');
        $two = $one->with(timeZone: 'MST');
        self::assertDateTime($one, 1970, 1, 1, 0, 0, 0, 0, 'EST');
        self::assertDateTime($two, 1970, 1, 1, 0, 0, 0, 0, 'MST');
    }

    public function testAll(): void
    {
        $one = DateTime::parse('0010-10-10 10:10:10.000010 EST', 'Y-m-d H:i:s.u e')->orFail();
        $two = $one->with(3, 3, 3, 3, 3, 3, 3, 'MST');
        self::assertDateTime($one, 10, 10, 10, 10, 10, 10, 10, 'EST');
        self::assertDateTime($two, 3, 3, 3, 3, 3, 3, 3, 'MST');
    }
}
