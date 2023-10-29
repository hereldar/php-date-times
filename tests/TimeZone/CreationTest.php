<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeZone as NativeTimeZone;
use Generator;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;
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

        self::assertSame($tz->getName(), $timeZone->name());
        self::assertSame($tz->getName(), (string) $timeZone);
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
        string $timeZoneName,
        TimeZoneType $timeZoneType,
    ): void {
        $tz = new NativeTimeZone($timeZoneName);
        $timeZone = TimeZone::of($timeZoneName);

        self::assertSame($tz->getName(), $timeZone->name());
        self::assertSame($tz->getName(), (string) $timeZone);
        self::assertSame($timeZoneType, $timeZone->type());
    }

    /**
     * @return list<array{0: string, 1: TimeZoneType}>
     */
    public static function validTimeZoneIdsAndOffsets(): array
    {
        return [
            ['Europe/London', TimeZoneType::Identifier],
            ['GMT+04:45', TimeZoneType::Offset],
            ['-06:00', TimeZoneType::Offset],
            ['CEST', TimeZoneType::Abbreviation],
        ];
    }

    /**
     * @dataProvider invalidTimeZoneIdsAndOffsets
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

    public static function invalidTimeZoneIdsAndOffsets(): Generator
    {
        $timeZones = [
            'Mars/Phobos',
            'Jupiter/Europa',
            'CET+04:45',
            '6:00',
            'BILBO',
        ];

        foreach ($timeZones as $timeZone) {
            yield [$timeZone];
        }
    }
}
