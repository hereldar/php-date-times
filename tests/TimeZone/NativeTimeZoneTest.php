<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeZone as NativeTimeZone;
use Generator;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\TimeZone;
use Hereldar\DateTimes\Tests\TestCase;
use Throwable;

final class NativeTimeZoneTest extends TestCase
{
    /**
     * @dataProvider validTimeZoneNames
     */
    public function testNativeTimeZone(
        string $timeZoneName,
    ): void {
        $nativeTimeZone = new NativeTimeZone($timeZoneName);
        $timeZone = TimeZone::of($timeZoneName);

        self::assertEquals($timeZone, TimeZone::fromNative($nativeTimeZone));
        self::assertEquals($nativeTimeZone, $timeZone->toNative());
    }

    public static function validTimeZoneNames(): Generator
    {
        $timeZoneNames = [
            'Europe/London',
            'GMT+04:45',
            '-06:00',
            'CEST',
        ];

        foreach ($timeZoneNames as $timeZoneName) {
            yield [$timeZoneName];
        }
    }

    /**
     * @dataProvider invalidTimeZoneNames
     */
    public function testTimeZoneException(
        string $timeZoneName,
    ): void {
        try {
            TimeZone::of($timeZoneName);
        } catch (Throwable $e) {
            self::assertInstanceOf(TimeZoneException::class, $e);
            self::assertSame($timeZoneName, $e->name());
        }
    }

    public static function invalidTimeZoneNames(): Generator
    {
        $timeZoneNames = [
            'Mars/Phobos',
            'Jupiter/Europa',
        ];

        foreach ($timeZoneNames as $timeZoneName) {
            yield [$timeZoneName];
        }
    }
}
