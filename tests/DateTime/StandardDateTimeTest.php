<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use DateTimeImmutable as StandardDateTime;
use Generator;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

final class StandardDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataForStandardDateTime
     */
    public function testStandardDateTime(
        StandardDateTime $stdDateTime,
        DateTime $expected,
    ): void {
        self::assertEquals($expected, DateTime::fromStandard($stdDateTime));
        self::assertEquals($stdDateTime, $expected->toStandard());
    }

    public static function dataForStandardDateTime(): Generator
    {
        yield [
            new StandardDateTime('2010-04-24 10:24:16'),
            DateTime::of(2010, 4, 24, 10, 24, 16),
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16-04:00'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 0, '-04:00'),
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16.763213+02:30'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, Offset::of(2, 30)),
        ];
        yield [
            new StandardDateTime('2010-04-24 10:24:16.763213 Europe/Madrid'),
            DateTime::of(2010, 4, 24, 10, 24, 16, 763213, TimeZone::of('Europe/Madrid')),
        ];
    }
}
