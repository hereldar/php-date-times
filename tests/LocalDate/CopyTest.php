<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testYear(): void
    {
        $one = LocalDate::of(year: 10);
        $two = $one->with(year: 3);
        self::assertLocalDate($one, 10, 1, 1);
        self::assertLocalDate($two, 3, 1, 1);
    }

    public function testMonth(): void
    {
        $one = LocalDate::of(month: 10);
        $two = $one->with(month: 3);
        self::assertLocalDate($one, 1970, 10, 1);
        self::assertLocalDate($two, 1970, 3, 1);
    }

    public function testDay(): void
    {
        $one = LocalDate::of(day: 10);
        $two = $one->with(day: 3);
        self::assertLocalDate($one, 1970, 1, 10);
        self::assertLocalDate($two, 1970, 1, 3);
    }

    public function testAll(): void
    {
        $one = LocalDate::parse('0010-10-10', 'Y-m-d')->orFail();
        $two = $one->with(3, 3, 3);
        self::assertLocalDate($one, 10, 10, 10);
        self::assertLocalDate($two, 3, 3, 3);
    }
}
