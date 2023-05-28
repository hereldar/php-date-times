<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use DateTime as MutableNativeDateTime;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use Generator;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class NativeTest extends TestCase
{
    /**
     * @dataProvider dataForNativeDateTime
     */
    public function testNativeDateTime(
        NativeDateTimeInterface $nativeDateTime,
        LocalDate $localDate,
        bool $reversible = true,
    ): void {
        self::assertEquals($localDate, LocalDate::fromNative($nativeDateTime));

        if ($reversible) {
            self::assertEquals($nativeDateTime, $localDate->toNative());
        } else {
            self::assertNotEquals($nativeDateTime, $localDate->toNative());
        }
    }

    public static function dataForNativeDateTime(): Generator
    {
        yield [
            new NativeDateTime('2010-04-24'),
            LocalDate::of(2010, 4, 24),
        ];
        yield [
            new NativeDateTime('2010-04-24 -04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16-04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new MutableNativeDateTime('2010-04-24'),
            LocalDate::of(2010, 4, 24),
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 -04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16-04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
    }
}
