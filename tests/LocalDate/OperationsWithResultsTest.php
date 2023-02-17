<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\LocalDate;

use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $date = LocalDate::of(1986)->add(years: 2)->orFail();
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame(1988, $date->year());

        $originalDate = LocalDate::parse('2020-06-04')->orFail();
        $period = Period::of(days: 4);
        $date = $originalDate->add($period)->orFail();
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-06-08', $date->format()->orFail());
        self::assertNotSame($date, $originalDate);

        $date = LocalDate
            ::parse('2020-06-23')->orFail()
            ->add(days: 23)->orFail()
        ;
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-07-16', $date->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $date->add(Period::of(1), 2)
        );
    }

    public function testSubtraction(): void
    {
        $date = LocalDate::of(1986)->subtract(years: 2)->orFail();
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame(1984, $date->year());

        $originalDate = LocalDate::parse('2020-06-04')->orFail();
        $period = Period::of(days: 4);
        $date = $originalDate->subtract($period)->orFail();
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-05-31', $date->format()->orFail());
        self::assertNotSame($date, $originalDate);

        $date = LocalDate
            ::parse('2020-06-23')->orFail()
            ->subtract(days: 23)->orFail()
        ;
        self::assertInstanceOf(LocalDate::class, $date);
        self::assertSame('2020-05-31', $date->format()->orFail());

        self::assertException(
            InvalidArgumentException::class,
            fn () => $date->subtract(Period::of(1), 2)
        );
    }
}
