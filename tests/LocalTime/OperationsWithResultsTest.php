<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalTime;

use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $time = LocalTime::of(12)->add(hours: 1)->orFail();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame(13, $time->hour());

        $originalDate = LocalTime::parse('17:05:08')->orFail();
        $period = Period::of(hours: 2);
        $time = $originalDate->add($period)->orFail();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame('19:05:08', $time->format()->orFail());
        self::assertNotSame($time, $originalDate);

        $time = LocalTime
            ::parse('08:05:45')->orFail()
            ->add(hours: 7)->orFail()
        ;
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame('15:05:45', $time->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $time->add(Period::of(1), 2)
        );
    }

    public function testSubtraction(): void
    {
        $time = LocalTime::of(12)->subtract(hours: 1)->orFail();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame(11, $time->hour());

        $originalDate = LocalTime::parse('17:05:08')->orFail();
        $period = Period::of(hours: 2);
        $time = $originalDate->subtract($period)->orFail();
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame('15:05:08', $time->format()->orFail());
        self::assertNotSame($time, $originalDate);

        $time = LocalTime
            ::parse('08:05:45')->orFail()
            ->subtract(hours: 7)->orFail()
        ;
        self::assertInstanceOf(LocalTime::class, $time);
        self::assertSame('01:05:45', $time->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $time->subtract(Period::of(1), 2)
        );
    }
}
