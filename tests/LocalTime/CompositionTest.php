<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class CompositionTest extends TestCase
{
    public function testDate(): void
    {
        $date = LocalDate::of(1, 2, 3);
        $time = LocalTime::of(4, 5, 6, 7);

        $dateTime = $time->atDate($date);

        self::assertLocalDateTime($dateTime, 1, 2, 3, 4, 5, 6, 7);
    }
}
