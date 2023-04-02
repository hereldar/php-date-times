<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use DateTimeImmutable as NativeDateTime;
use Generator;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class NativeDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataForNativeDateTime
     */
    public function testNativeDateTime(
        NativeDateTime $nativeDateTime,
        LocalTime $localTime,
        bool $reversible = true,
    ): void {
        self::assertEquals($localTime, LocalTime::fromNative($nativeDateTime));

        if ($reversible) {
            self::assertEquals($nativeDateTime, $localTime->toNative());
        } else {
            self::assertNotEquals($nativeDateTime, $localTime->toNative());
        }
    }

    public static function dataForNativeDateTime(): Generator
    {
        yield [
            new NativeDateTime('10:24:16'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new NativeDateTime('10:24:16-04:00'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16-04:00'),
            LocalTime::of(10, 24, 16),
            false,
        ];
    }
}
