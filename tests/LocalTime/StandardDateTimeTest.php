<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use DateTimeImmutable as StandardDateTime;
use Generator;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Tests\TestCase;

final class StandardDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataForStandardDateTime
     */
    public function testStandardDateTime(
        StandardDateTime $stdDateTime,
        LocalTime $expected,
        bool $reversible = true,
    ): void {
        self::assertEquals($expected, LocalTime::fromStandard($stdDateTime));

        if ($reversible) {
            self::assertEquals($stdDateTime, $expected->toStandard());
        } else {
            self::assertNotEquals($stdDateTime, $expected->toStandard());
        }
    }

    public static function dataForStandardDateTime(): Generator
    {
        yield [
            new StandardDateTime('10:24:16'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new StandardDateTime('10:24:16-04:00'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16'),
            LocalTime::of(10, 24, 16),
            false,
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16-04:00'),
            LocalTime::of(10, 24, 16),
            false,
        ];
    }
}
