<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class CompositionTest extends TestCase
{
    public function testTime(): void
    {
        $date = LocalDate::of(1, 2, 3);
        $time = LocalTime::of(4, 5, 6, 7);

        $dateTime = $date->atTime($time);

        self::assertLocalDateTime($dateTime, 1, 2, 3, 4, 5, 6, 7);
    }
}
