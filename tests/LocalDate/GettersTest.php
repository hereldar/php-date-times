<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class GettersTest extends TestCase
{
    public function testYear(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(1986, $date->year());
    }

    public function testMonth(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(12, $date->month());
    }

    public function testWeek(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(52, $date->week());
    }

    public function testWeekYear(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(1986, $date->weekYear());
    }

    public function testDay(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(25, $date->day());
    }

    public function testDayOfWeek(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(4, $date->dayOfWeek());
    }

    public function testDayOfYear(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertSame(359, $date->dayOfYear());
    }

    public function testInLeapYear(): void
    {
        $date = LocalDate::of(1986, 12, 25);
        self::assertFalse($date->inLeapYear());
    }
}
