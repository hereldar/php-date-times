<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;
use InvalidArgumentException;
use OutOfRangeException;

final class CompositionTest extends TestCase
{
    public function testTimeZone(): void
    {
        $localDateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);

        $dateTime1 = $localDateTime->atTimeZone(TimeZone::of('America/New_York'));
        self::assertDateTime($dateTime1, 1, 2, 3, 4, 5, 6, 7, 'America/New_York');

        $dateTime2 = $localDateTime->atTimeZone('America/New_York');
        self::assertDateTime($dateTime2, 1, 2, 3, 4, 5, 6, 7, 'America/New_York');
    }

    public function testOffset(): void
    {
        $localDateTime = LocalDateTime::of(1, 2, 3, 4, 5, 6, 7);

        $dateTime1 = $localDateTime->atOffset(Offset::of(1, 45));
        self::assertDateTime($dateTime1, 1, 2, 3, 4, 5, 6, 7, '+01:45');

        $dateTime2 = $localDateTime->atOffset(1, 45);
        self::assertDateTime($dateTime2, 1, 2, 3, 4, 5, 6, 7, '+01:45');

        self::assertException(
            InvalidArgumentException::class,
            fn() => $localDateTime->atOffset(Offset::of(1), 45)
        );
    }

    public function testInvalidHours(): void
    {
        $localDateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, -16 given'),
            fn() => $localDateTime->atOffset(hours: -16)
        );
        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, 16 given'),
            fn() => $localDateTime->atOffset(16)
        );
    }

    public function testInvalidMinutes(): void
    {
        $localDateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, -60 given'),
            fn() => $localDateTime->atOffset(minutes: -60)
        );
        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, 60 given'),
            fn() => $localDateTime->atOffset(0, 60)
        );
    }

    public function testInvalidSeconds(): void
    {
        $localDateTime = LocalDateTime::epoch();

        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, -60 given'),
            fn() => $localDateTime->atOffset(seconds: -60)
        );
        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, 60 given'),
            fn() => $localDateTime->atOffset(0, 0, 60)
        );
    }
}
