<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use DateTimeImmutable as StandardDateTime;
use Generator;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class StandardDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataForStandardDateTime
     */
    public function testStandardDateTime(
        StandardDateTime $stdDateTime,
        LocalDateTime $expected,
        bool $reversible = true,
    ): void {
        self::assertEquals($expected, LocalDateTime::fromStandard($stdDateTime));

        if ($reversible) {
            self::assertEquals($stdDateTime, $expected->toStandard());
        } else {
            self::assertNotEquals($stdDateTime, $expected->toStandard());
        }
    }

    public static function dataForStandardDateTime(): Generator
    {
        yield [
            new StandardDateTime('2010-04-24 10:24:16'),
            LocalDateTime::of(2010, 4, 24, 10, 24, 16),
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16-04:00'),
            LocalDateTime::of(2010, 4, 24, 10, 24, 16),
            false,
        ];
    }
}
