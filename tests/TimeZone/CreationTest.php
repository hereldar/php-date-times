<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeZone as NativeTimeZone;
use Generator;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\TimeZone;
use Hereldar\DateTimes\Tests\TestCase;
use Throwable;

final class CreationTest extends TestCase
{
    /**
     * @dataProvider validTimeZoneIds
     */
    public function testSystemTimeZone(
        string $timeZoneId,
    ): void {
        $tz = new NativeTimeZone($timeZoneId);
        date_default_timezone_set($tz->getName());
        $timeZone = TimeZone::system();

        self::assertEquals($tz->getName(), $timeZone->name());
    }

    public static function validTimeZoneIds(): Generator
    {
        $timeZones = [
            'Europe/Lisbon',
            'Asia/Hong_Kong',
            'Africa/Lagos',
            'America/Montevideo',
            date_default_timezone_get(),
        ];

        foreach ($timeZones as $timeZone) {
            yield [$timeZone];
        }
    }

    /**
     * @dataProvider validTimeZoneIdsAndOffsets
     */
    public function testCustomTimeZone(
        string $timeZoneId,
    ): void {
        $tz = new NativeTimeZone($timeZoneId);
        $timeZone = TimeZone::of($timeZoneId);

        self::assertEquals($tz->getName(), $timeZone->name());
    }

    public static function validTimeZoneIdsAndOffsets(): Generator
    {
        $timeZones = [
            'Europe/London',
            'GMT+04:45',
            '-06:00',
            'CEST',
        ];

        foreach ($timeZones as $timeZone) {
            yield [$timeZone];
        }
    }

    /**
     * @dataProvider invalidTimeZoneIds
     */
    public function testTimeZoneException(
        string $timeZoneId,
    ): void {
        try {
            TimeZone::of($timeZoneId);
        } catch (Throwable $e) {
            self::assertInstanceOf(TimeZoneException::class, $e);
            self::assertSame($timeZoneId, $e->name());
        }
    }

    public static function invalidTimeZoneIds(): Generator
    {
        $timeZones = [
            'Mars/Phobos',
            'Jupiter/Europa',
        ];

        foreach ($timeZones as $timeZone) {
            yield [$timeZone];
        }
    }
}
