<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use DateTime as MutableNativeDateTime;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use Generator;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class NativeTest extends TestCase
{
    /**
     * @dataProvider dataForNativeDateTime
     */
    public function testNativeDateTime(
        NativeDateTimeInterface $nativeDateTime,
        DateTime $dateTime,
    ): void {
        self::assertEquals($dateTime, DateTime::fromNative($nativeDateTime));
        self::assertEquals($nativeDateTime, $dateTime->toNative());
    }

    public static function dataForNativeDateTime(): Generator
    {
        yield [
            new NativeDateTime('2010-04-24 10:24:16'),
            DateTime::of(2010, 4, 24, 10, 24, 16),
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16-04:00'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 0, '-04:00'),
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16.763213+02:30'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, Offset::of(2, 30)),
        ];
        yield [
            new NativeDateTime('2010-04-24 10:24:16.763213 Europe/Madrid'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, TimeZone::of('Europe/Madrid')),
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16'),
            DateTime::of(2010, 4, 24, 10, 24, 16),
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16-04:00'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 0, '-04:00'),
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16.763213+02:30'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, Offset::of(2, 30)),
        ];
        yield [
            new MutableNativeDateTime('2010-04-24 10:24:16.763213 Europe/Madrid'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, TimeZone::of('Europe/Madrid')),
        ];
    }
}
