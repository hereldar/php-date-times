<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testYear(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(1986, $dateTime->year());
    }

    public function testMonth(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(12, $dateTime->month());
    }

    public function testWeek(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(52, $dateTime->week());
    }

    public function testWeekYear(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(1986, $dateTime->weekYear());
    }

    public function testDay(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(25, $dateTime->day());
    }

    public function testDayOfWeek(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertSame(4, $dateTime->dayOfWeek());
    }

    public function testInLeapYear(): void
    {
        $dateTime = LocalDate::of(1986, 12, 25);
        self::assertFalse($dateTime->inLeapYear());
    }
}
