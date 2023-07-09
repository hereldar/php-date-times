<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testYear(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(1986, $dateTime->year());
    }

    public function testMonth(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(12, $dateTime->month());
    }

    public function testWeek(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(52, $dateTime->week());
    }

    public function testWeekYear(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(1986, $dateTime->weekYear());
    }

    public function testDay(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(25, $dateTime->day());
    }

    public function testDayOfWeek(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(4, $dateTime->dayOfWeek());
    }

    public function testDayOfYear(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(359, $dateTime->dayOfYear());
    }

    public function testInLeapYear(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertFalse($dateTime->inLeapYear());
    }

    public function testHour(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(12, $dateTime->hour());
    }

    public function testMinute(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(30, $dateTime->minute());
    }

    public function testSecond(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(25, $dateTime->second());
    }

    public function testMillisecond(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(0, $dateTime->millisecond());

        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999_999);
        self::assertSame(999, $dateTime->millisecond());
    }

    public function testMicrosecond(): void
    {
        $dateTime = LocalDateTime::of(1986, 12, 25, 12, 30, 25, 999);
        self::assertSame(999, $dateTime->microsecond());
    }
}
