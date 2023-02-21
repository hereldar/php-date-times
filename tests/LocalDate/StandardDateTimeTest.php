<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use DateTimeImmutable as StandardDateTime;
use Generator;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Tests\TestCase;

final class StandardDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataForStandardDateTime
     */
    public function testStandardDateTime(
        StandardDateTime $stdDateTime,
        LocalDate $expected,
        bool $reversible = true,
    ): void {
        self::assertEquals($expected, LocalDate::fromStandard($stdDateTime));

        if ($reversible) {
            self::assertEquals($stdDateTime, $expected->toStandard());
        } else {
            self::assertNotEquals($stdDateTime, $expected->toStandard());
        }
    }

    public static function dataForStandardDateTime(): Generator
    {
        yield [
            new StandardDateTime('2010-04-24'),
            LocalDate::of(2010, 4, 24),
        ];
        yield [
            new StandardDateTime('2010-04-24 -04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16-04:00'),
            LocalDate::of(2010, 4, 24),
            false,
        ];
    }
}
