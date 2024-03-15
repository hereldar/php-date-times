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
     *
     * @param non-empty-string $timeZoneId
     */
    public function testSystemTimeZone(
        string $timeZoneId,
    ): void {
        $tz = new NativeTimeZone($timeZoneId);
        \date_default_timezone_set($tz->getName());
        $timeZone = TimeZone::system();

        self::assertSame($tz->getName(), $timeZone->name());
        self::assertSame($tz->getName(), (string) $timeZone);
    }

    /**
     * @return Generator<int, array{non-empty-string}>
     */
    public static function validTimeZoneIds(): Generator
    {
        $timeZones = [
            'Europe/Lisbon',
            'Asia/Hong_Kong',
            'Africa/Lagos',
            'America/Montevideo',
            \date_default_timezone_get(),
        ];

        foreach ($timeZones as $timeZone) {
            /** @phpstan-ignore-next-line */
            yield [$timeZone];
        }
    }

    /**
     * @dataProvider validTimeZoneIdsAndOffsets
     *
     * @param non-empty-string $timeZoneName
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
     * @return list<array{0: non-empty-string, 1: TimeZoneType}>
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
     *
     * @param non-empty-string $timeZoneName
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

    /**
     * @return Generator<int, array{non-empty-string}>
     */
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
