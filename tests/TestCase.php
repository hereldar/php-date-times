<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests;

use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\IPeriod;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string<Throwable> $expectedException
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public static function assertException(
        string $expectedException,
        callable $callback
    ): void {
        try {
            $callback();
            $exception = null;
        } catch (Throwable $exception) {
        }
        /** @psalm-suppress PossiblyUndefinedVariable */
        static::assertThat(
            $exception,
            new ExceptionConstraint(
                $expectedException
            )
        );
    }

    public static function assertDateTime(
        IDateTime $dateTime,
        int $year,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
        ?string $timeZone = null,
    ): void {
        $actual = ['year' => $dateTime->year()];

        $expected = ['year' => $year];

        if ($month !== null) {
            $actual['month'] = $dateTime->month();
            $expected['month'] = $month;
        }

        if ($day !== null) {
            $actual['day'] = $dateTime->day();
            $expected['day'] = $day;
        }

        if ($hour !== null) {
            $actual['hour'] = $dateTime->hour();
            $expected['hour'] = $hour;
        }

        if ($minute !== null) {
            $actual['minute'] = $dateTime->minute();
            $expected['minute'] = $minute;
        }

        if ($second !== null) {
            $actual['second'] = $dateTime->second();
            $expected['second'] = $second;
        }

        if ($microsecond !== null) {
            $actual['microsecond'] = $dateTime->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        if ($timeZone !== null) {
            $actual['timeZone'] = $dateTime->timezone()->name();
            $expected['timeZone'] = $timeZone;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalDate(
        ILocalDate $date,
        int $year,
        ?int $month = null,
        ?int $day = null,
    ): void {
        $actual = ['year' => $date->year()];

        $expected = ['year' => $year];

        if ($month !== null) {
            $actual['month'] = $date->month();
            $expected['month'] = $month;
        }

        if ($day !== null) {
            $actual['day'] = $date->day();
            $expected['day'] = $day;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalDateTime(
        ILocalDateTime $dateTime,
        int $year,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): void {
        $actual = ['year' => $dateTime->year()];

        $expected = ['year' => $year];

        if ($month !== null) {
            $actual['month'] = $dateTime->month();
            $expected['month'] = $month;
        }

        if ($day !== null) {
            $actual['day'] = $dateTime->day();
            $expected['day'] = $day;
        }

        if ($hour !== null) {
            $actual['hour'] = $dateTime->hour();
            $expected['hour'] = $hour;
        }

        if ($minute !== null) {
            $actual['minute'] = $dateTime->minute();
            $expected['minute'] = $minute;
        }

        if ($second !== null) {
            $actual['second'] = $dateTime->second();
            $expected['second'] = $second;
        }

        if ($microsecond !== null) {
            $actual['microsecond'] = $dateTime->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalTime(
        ILocalTime $time,
        int $hour,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): void {
        $actual = ['hour' => $time->hour()];

        $expected = ['hour' => $hour];

        if ($minute !== null) {
            $actual['minute'] = $time->minute();
            $expected['minute'] = $minute;
        }

        if ($second !== null) {
            $actual['second'] = $time->second();
            $expected['second'] = $second;
        }

        if ($microsecond !== null) {
            $actual['microsecond'] = $time->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertOffset(
        IOffset $times,
        int $hours,
        ?int $minutes = null,
        ?int $seconds = null,
    ): void {
        $actual = ['hours' => $times->hours()];

        $expected = ['hours' => $hours];

        if ($minutes !== null) {
            $actual['minutes'] = $times->minutes();
            $expected['minutes'] = $minutes;
        }

        if ($seconds !== null) {
            $actual['seconds'] = $times->seconds();
            $expected['seconds'] = $seconds;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertPeriod(
        IPeriod $period,
        int $years,
        ?int $months = null,
        ?int $days = null,
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
        ?int $microseconds = null,
    ): void {
        $actual = ['years' => $period->years()];

        $expected = ['years' => $years];

        if ($months !== null) {
            $actual['months'] = $period->months();
            $expected['months'] = $months;
        }

        if ($days !== null) {
            $actual['days'] = $period->days();
            $expected['days'] = $days;
        }

        if ($hours !== null) {
            $actual['hours'] = $period->hours();
            $expected['hours'] = $hours;
        }

        if ($minutes !== null) {
            $actual['minutes'] = $period->minutes();
            $expected['minutes'] = $minutes;
        }

        if ($seconds !== null) {
            $actual['seconds'] = $period->seconds();
            $expected['seconds'] = $seconds;
        }

        if ($microseconds !== null) {
            $actual['microseconds'] = $period->microseconds();
            $expected['microseconds'] = $microseconds;
        }

        static::assertSame($expected, $actual);
    }
}
