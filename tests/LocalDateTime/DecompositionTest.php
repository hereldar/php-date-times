<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class DecompositionTest extends TestCase
{
    public function testDate(): void
    {
        $dateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);

        self::assertLocalDate($dateTime->date(), 1, 2, 3);
    }

    public function testTime(): void
    {
        $dateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);

        self::assertLocalTime($dateTime->time(), 4, 5, 6, 7);
    }
}
