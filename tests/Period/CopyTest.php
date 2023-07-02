<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testYears(): void
    {
        $one = Period::of(years: 1);
        $two = $one->with(years: 2);
        $three = $one->copy(years: 3)->orFail();
        self::assertPeriod($one, 1, 0, 0, 0, 0, 0, 0);
        self::assertPeriod($two, 2, 0, 0, 0, 0, 0, 0);
        self::assertPeriod($three, 3, 0, 0, 0, 0, 0, 0);
    }

    public function testMonths(): void
    {
        $one = Period::of(months: 1);
        $two = $one->with(months: 2);
        $three = $one->copy(months: 3)->orFail();
        self::assertPeriod($one, 0, 1, 0, 0, 0, 0, 0);
        self::assertPeriod($two, 0, 2, 0, 0, 0, 0, 0);
        self::assertPeriod($three, 0, 3, 0, 0, 0, 0, 0);
    }

    public function testDays(): void
    {
        $one = Period::of(days: 1);
        $two = $one->with(days: 2);
        $three = $one->copy(days: 3)->orFail();
        self::assertPeriod($one, 0, 0, 1, 0, 0, 0, 0);
        self::assertPeriod($two, 0, 0, 2, 0, 0, 0, 0);
        self::assertPeriod($three, 0, 0, 3, 0, 0, 0, 0);
    }

    public function testHours(): void
    {
        $one = Period::of(hours: 1);
        $two = $one->with(hours: 2);
        $three = $one->copy(hours: 3)->orFail();
        self::assertPeriod($one, 0, 0, 0, 1, 0, 0);
        self::assertPeriod($two, 0, 0, 0, 2, 0, 0);
        self::assertPeriod($three, 0, 0, 0, 3, 0, 0);
    }

    public function testMinutes(): void
    {
        $one = Period::of(minutes: 1);
        $two = $one->with(minutes: 2);
        $three = $one->copy(minutes: 3)->orFail();
        self::assertPeriod($one, 0, 0, 0, 0, 1, 0, 0);
        self::assertPeriod($two, 0, 0, 0, 0, 2, 0, 0);
        self::assertPeriod($three, 0, 0, 0, 0, 3, 0, 0);
    }

    public function testSeconds(): void
    {
        $one = Period::of(seconds: 1);
        $two = $one->with(seconds: 2);
        $three = $one->copy(seconds: 3)->orFail();
        self::assertPeriod($one, 0, 0, 0, 0, 0, 1, 0);
        self::assertPeriod($two, 0, 0, 0, 0, 0, 2, 0);
        self::assertPeriod($three, 0, 0, 0, 0, 0, 3, 0);
    }

    public function testMicroseconds(): void
    {
        $one = Period::of(microseconds: 1);
        $two = $one->with(microseconds: 2);
        $three = $one->copy(microseconds: 3)->orFail();
        self::assertPeriod($one, 0, 0, 0, 0, 0, 0, 1);
        self::assertPeriod($two, 0, 0, 0, 0, 0, 0, 2);
        self::assertPeriod($three, 0, 0, 0, 0, 0, 0, 3);
    }

    public function testAll(): void
    {
        $one = Period::parse('1-1-1 1:1:1.1', '%y-%m-%d %h:%i:%s.%u')->orFail();
        $two = $one->with(2, 2, 2, 2, 2, 2, 2);
        $three = $one->copy(3, 3, 3, 3, 3, 3, 3)->orFail();
        self::assertPeriod($one, 1, 1, 1, 1, 1, 1, 1);
        self::assertPeriod($two, 2, 2, 2, 2, 2, 2, 2);
        self::assertPeriod($three, 3, 3, 3, 3, 3, 3, 3);
    }
}
