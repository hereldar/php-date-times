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

        self::assertLocalDate(
            $dateTime->date(),
            1, 2, 3
        );
        self::assertNativeDateTime(
            $dateTime->date()->toNative(),
            1, 2, 3, 0, 0, 0, 0, 'UTC'
        );
    }

    public function testTime(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        self::assertLocalTime(
            $dateTime->time(),
            4, 5, 6, 7
        );
        self::assertNativeDateTime(
            $dateTime->time()->toNative(),
            1970, 1, 1, 4, 5, 6, 7, 'UTC'
        );
    }

    public function testTimeZone(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        self::assertTimeZone(
            $dateTime->timeZone(),
            'America/New_York'
        );
    }

    public function testOffset(): void
    {
        $dateTime = DateTime::of(1, 2, 3, 4, 5, 6, 7, '+04:30');

        self::assertOffset(
            $dateTime->offset(),
            4, 30, 0
        );
    }
}
