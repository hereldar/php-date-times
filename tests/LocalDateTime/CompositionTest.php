<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class CompositionTest extends TestCase
{
    public function testTimeZone(): void
    {
        $localDateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);
        $timeZone = TimeZone::of('America/New_York');

        $dateTime = $localDateTime->atTimeZone($timeZone);

        self::assertDateTime($dateTime, 1, 2, 3, 4, 5, 6, 7, 'America/New_York');
    }

    public function testOffset(): void
    {
        $localDateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);
        $offset = Offset::of(1);

        $dateTime = $localDateTime->atOffset($offset);

        self::assertDateTime($dateTime, 1, 2, 3, 4, 5, 6, 7, '+01:00');
    }
}
