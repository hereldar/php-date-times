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
        $dateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);

        self::assertLocalTime(
            $dateTime->time(),
            4, 5, 6, 7
        );
        self::assertNativeDateTime(
            $dateTime->time()->toNative(),
            1970, 1, 1, 4, 5, 6, 7, 'UTC'
        );
    }
}
