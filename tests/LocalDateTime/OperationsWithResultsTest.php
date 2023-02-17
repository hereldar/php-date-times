<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDateTime;

use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $dateTime = LocalDateTime::of(1986)->add(years: 2)->orFail();
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame(1988, $dateTime->year());

        $originalDate = LocalDateTime::parse('2020-06-04T17:05:08')->orFail();
        $period = Period::of(days: 4, hours: 2);
        $dateTime = $originalDate->add($period)->orFail();
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame('2020-06-08T19:05:08', $dateTime->format()->orFail());
        self::assertNotSame($dateTime, $originalDate);

        $dateTime = LocalDateTime
            ::parse('2020-06-23T08:05:45')->orFail()
            ->add(days: 23, hours: 7)->orFail()
        ;
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame('2020-07-16T15:05:45', $dateTime->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $dateTime->add(Period::of(1), 2)
        );
    }

    public function testSubtraction(): void
    {
        $dateTime = LocalDateTime::of(1986)->subtract(years: 2)->orFail();
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame(1984, $dateTime->year());

        $originalDate = LocalDateTime::parse('2020-06-04T17:05:08')->orFail();
        $period = Period::of(days: 4, hours: 2);
        $dateTime = $originalDate->subtract($period)->orFail();
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame('2020-05-31T15:05:08', $dateTime->format()->orFail());
        self::assertNotSame($dateTime, $originalDate);

        $dateTime = LocalDateTime
            ::parse('2020-06-23T08:05:45')->orFail()
            ->subtract(days: 23, hours: 7)->orFail()
        ;
        self::assertInstanceOf(LocalDateTime::class, $dateTime);
        self::assertSame('2020-05-31T01:05:45', $dateTime->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $dateTime->subtract(Period::of(1), 2)
        );
    }
}
