<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class DecompositionTest extends TestCase
{
    public function testDate(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        self::assertLocalDate($dateTime->date(), 1, 2, 3);
    }

    public function testTime(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        self::assertLocalTime($dateTime->time(), 4, 5, 6, 7);
    }

    public function testTimeZone(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        self::assertTimeZone($dateTime->timeZone(), 'America/New_York');
    }
}
