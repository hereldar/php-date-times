<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeZone as NativeTimeZone;
use Generator;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class NativeTest extends TestCase
{
    /**
     * @dataProvider validTimeZones
     *
     * @param non-empty-string $timeZoneName
     */
    public function testNativeTimeZone(
        string $timeZoneName,
    ): void {
        $nativeTimeZone = new NativeTimeZone($timeZoneName);
        $timeZone = TimeZone::of($timeZoneName);

        self::assertEquals($timeZone, TimeZone::fromNative($nativeTimeZone));
        self::assertEquals($nativeTimeZone, $timeZone->toNative());
    }

    /**
     * @return Generator<int, array{non-empty-string}>
     */
    public static function validTimeZones(): Generator
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
}
