<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $dateTime = DateTime::of(1986)->add(years: 2)->orFail();
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame(1988, $dateTime->year());

        $originalDate = DateTime::parse('2020-06-04T17:05:08Z')->orFail();
        $period = Period::of(days: 4, hours: 2);
        $dateTime = $originalDate->add($period)->orFail();
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame('2020-06-08T19:05:08Z', $dateTime->format()->orFail());
        self::assertNotSame($dateTime, $originalDate);

        $dateTime = DateTime
            ::parse('2020-06-23T08:05:45Z')->orFail()
            ->add(days: 23, hours: 7)->orFail()
        ;
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame('2020-07-16T15:05:45Z', $dateTime->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $dateTime->add(Period::of(1), 2)
        );
    }

    public function testSubtraction(): void
    {
        $dateTime = DateTime::of(1986)->subtract(years: 2)->orFail();
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame(1984, $dateTime->year());

        $originalDate = DateTime::parse('2020-06-04T17:05:08Z')->orFail();
        $period = Period::of(days: 4, hours: 2);
        $dateTime = $originalDate->subtract($period)->orFail();
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame('2020-05-31T15:05:08Z', $dateTime->format()->orFail());
        self::assertNotSame($dateTime, $originalDate);

        $dateTime = DateTime
            ::parse('2020-06-23T08:05:45Z')->orFail()
            ->subtract(days: 23, hours: 7)->orFail()
        ;
        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame('2020-05-31T01:05:45Z', $dateTime->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $dateTime->subtract(Period::of(1), 2)
        );
    }
}
