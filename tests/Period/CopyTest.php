<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testYears(): void
    {
        $one = Period::of(years: 10);
        $two = $one->with(years: 3);
        self::assertPeriod($one, 10, 0, 0, 0, 0, 0, 0);
        self::assertPeriod($two, 3, 0, 0, 0, 0, 0, 0);
    }

    public function testMonths(): void
    {
        $one = Period::of(months: 10);
        $two = $one->with(months: 3);
        self::assertPeriod($one, 0, 10, 0, 0, 0, 0, 0);
        self::assertPeriod($two, 0, 3, 0, 0, 0, 0, 0);
    }

    public function testDays(): void
    {
        $one = Period::of(days: 10);
        $two = $one->with(days: 3);
        self::assertPeriod($one, 0, 0, 10, 0, 0, 0, 0);
        self::assertPeriod($two, 0, 0, 3, 0, 0, 0, 0);
    }

    public function testHours(): void
    {
        $one = Period::of(hours: 10);
        $two = $one->with(hours: 3);
        self::assertPeriod($one, 0, 0, 0, 10, 0, 0);
        self::assertPeriod($two, 0, 0, 0, 3, 0, 0);
    }

    public function testMinutes(): void
    {
        $one = Period::of(minutes: 10);
        $two = $one->with(minutes: 3);
        self::assertPeriod($one, 0, 0, 0, 0, 10, 0, 0);
        self::assertPeriod($two, 0, 0, 0, 0, 3, 0, 0);
    }

    public function testSeconds(): void
    {
        $one = Period::of(seconds: 10);
        $two = $one->with(seconds: 3);
        self::assertPeriod($one, 0, 0, 0, 0, 0, 10, 0);
        self::assertPeriod($two, 0, 0, 0, 0, 0, 3, 0);
    }

    public function testMicroseconds(): void
    {
        $one = Period::of(microseconds: 10);
        $two = $one->with(microseconds: 3);
        self::assertPeriod($one, 0, 0, 0, 0, 0, 0, 10);
        self::assertPeriod($two, 0, 0, 0, 0, 0, 0, 3);
    }

    public function testAll(): void
    {
        $one = Period::parse('10-10-10 10:10:10.10', '%y-%m-%d %h:%i:%s.%u')->orFail();
        $two = $one->with(3, 3, 3, 3, 3, 3, 3);
        self::assertPeriod($one, 10, 10, 10, 10, 10, 10, 10);
        self::assertPeriod($two, 3, 3, 3, 3, 3, 3, 3);
    }
}
