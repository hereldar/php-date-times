<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use DateTimeImmutable as NativeDateTime;
use Generator;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class NativeTest extends TestCase
{
    /**
     * @dataProvider dataForNativeDateTime
     */
    public function testNativeDateTime(
        NativeDateTime $nativeDateTime,
        LocalDateTime $localDateTime,
        bool $reversible = true,
    ): void {
        self::assertEquals($localDateTime, LocalDateTime::fromNative($nativeDateTime));

        if ($reversible) {
            self::assertEquals($nativeDateTime, $localDateTime->toNative());
        } else {
            self::assertNotEquals($nativeDateTime, $localDateTime->toNative());
        }
    }

    public static function dataForNativeDateTime(): Generator
    {
        yield [
            new NativeDateTime('2010-04-24 10:24:16'),
            LocalDateTime::of(2010, 4, 24, 10, 24, 16),
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16-04:00'),
            LocalDateTime::of(2010, 4, 24, 10, 24, 16),
            false,
        ];
    }
}
