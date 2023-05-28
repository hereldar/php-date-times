<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testYear(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(1986, $dateTime->year());
    }

    public function testMonth(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(12, $dateTime->month());
    }

    public function testWeek(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(52, $dateTime->week());
    }

    public function testWeekYear(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(1986, $dateTime->weekYear());
    }

    public function testDay(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(25, $dateTime->day());
    }

    public function testDayOfWeek(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(4, $dateTime->dayOfWeek());
    }

    public function testInLeapYear(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertFalse($dateTime->inLeapYear());
    }

    public function testHour(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(12, $dateTime->hour());
    }

    public function testMinute(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(30, $dateTime->minute());
    }

    public function testSecond(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(25, $dateTime->second());
    }

    public function testMillisecond(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(0, $dateTime->millisecond());

        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999_999);
        self::assertSame(999, $dateTime->millisecond());
    }

    public function testMicrosecond(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(999, $dateTime->microsecond());
    }

    public function testInDaylightSavingTime(): void
    {
        $dateTime = DateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertFalse($dateTime->inDaylightSavingTime());
    }
}
