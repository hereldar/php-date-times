<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests;

use DateTimeImmutable as NativeDateTime;
use Generator;
use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\LocalTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\TimeZone;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\ExceptionCode;
use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param Throwable|class-string<Throwable> $expectedException
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public static function assertException(
        Throwable|string $expectedException,
        callable $callback
    ): void {
        $exception = null;

        try {
            $callback();
        } catch (Throwable $exception) {
        }

        if (\is_string($expectedException)) {
            static::assertThat(
                $exception,
                new ExceptionConstraint($expectedException)
            );
        } else {
            static::assertThat(
                $exception,
                new ExceptionConstraint($expectedException::class)
            );
            static::assertThat(
                $exception?->getMessage(),
                new ExceptionMessageIsOrContains($expectedException->getMessage())
            );
            static::assertThat(
                $exception?->getCode(),
                new ExceptionCode($expectedException->getCode())
            );
        }
    }

    public static function assertNativeDateTime(
        NativeDateTime $dateTime,
        int $year,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
        ?string $timeZone = null,
    ): void {
        $actual = ['year' => (int) $dateTime->format('Y')];

        $expected = ['year' => $year];

        if (null !== $month) {
            $actual['month'] = (int) $dateTime->format('n');
            $expected['month'] = $month;
        }

        if (null !== $day) {
            $actual['day'] = (int) $dateTime->format('j');
            $expected['day'] = $day;
        }

        if (null !== $hour) {
            $actual['hour'] = (int) $dateTime->format('G');
            $expected['hour'] = $hour;
        }

        if (null !== $minute) {
            $actual['minute'] = (int) $dateTime->format('i');
            $expected['minute'] = $minute;
        }

        if (null !== $second) {
            $actual['second'] = (int) $dateTime->format('s');
            $expected['second'] = $second;
        }

        if (null !== $microsecond) {
            $actual['microsecond'] = (int) $dateTime->format('u');
            $expected['microsecond'] = $microsecond;
        }

        if (null !== $timeZone) {
            $actual['timeZone'] = $dateTime->getTimezone()->getName();
            $expected['timeZone'] = $timeZone;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertDateTime(
        DateTime $dateTime,
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

        if (null !== $month) {
            $actual['month'] = $dateTime->month();
            $expected['month'] = $month;
        }

        if (null !== $day) {
            $actual['day'] = $dateTime->day();
            $expected['day'] = $day;
        }

        if (null !== $hour) {
            $actual['hour'] = $dateTime->hour();
            $expected['hour'] = $hour;
        }

        if (null !== $minute) {
            $actual['minute'] = $dateTime->minute();
            $expected['minute'] = $minute;
        }

        if (null !== $second) {
            $actual['second'] = $dateTime->second();
            $expected['second'] = $second;
        }

        if (null !== $microsecond) {
            $actual['microsecond'] = $dateTime->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        if (null !== $timeZone) {
            $actual['timeZone'] = $dateTime->timeZone()->name();
            $expected['timeZone'] = $timeZone;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalDate(
        LocalDate $date,
        int $year,
        ?int $month = null,
        ?int $day = null,
    ): void {
        $actual = ['year' => $date->year()];

        $expected = ['year' => $year];

        if (null !== $month) {
            $actual['month'] = $date->month();
            $expected['month'] = $month;
        }

        if (null !== $day) {
            $actual['day'] = $date->day();
            $expected['day'] = $day;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalDateTime(
        LocalDateTime $dateTime,
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

        if (null !== $month) {
            $actual['month'] = $dateTime->month();
            $expected['month'] = $month;
        }

        if (null !== $day) {
            $actual['day'] = $dateTime->day();
            $expected['day'] = $day;
        }

        if (null !== $hour) {
            $actual['hour'] = $dateTime->hour();
            $expected['hour'] = $hour;
        }

        if (null !== $minute) {
            $actual['minute'] = $dateTime->minute();
            $expected['minute'] = $minute;
        }

        if (null !== $second) {
            $actual['second'] = $dateTime->second();
            $expected['second'] = $second;
        }

        if (null !== $microsecond) {
            $actual['microsecond'] = $dateTime->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertLocalTime(
        LocalTime $time,
        int $hour,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): void {
        $actual = ['hour' => $time->hour()];

        $expected = ['hour' => $hour];

        if (null !== $minute) {
            $actual['minute'] = $time->minute();
            $expected['minute'] = $minute;
        }

        if (null !== $second) {
            $actual['second'] = $time->second();
            $expected['second'] = $second;
        }

        if (null !== $microsecond) {
            $actual['microsecond'] = $time->microsecond();
            $expected['microsecond'] = $microsecond;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertOffset(
        Offset $offset,
        int $hours,
        ?int $minutes = null,
        ?int $seconds = null,
    ): void {
        $actual = ['hours' => $offset->hours()];

        $expected = ['hours' => $hours];

        if (null !== $minutes) {
            $actual['minutes'] = $offset->minutes();
            $expected['minutes'] = $minutes;
        }

        if (null !== $seconds) {
            $actual['seconds'] = $offset->seconds();
            $expected['seconds'] = $seconds;
        }

        static::assertSame($expected, $actual);
    }

    public static function assertTimeZone(
        TimeZone $timeZone,
        string $name,
    ): void {
        static::assertSame($name, $timeZone->name());
    }

    public static function assertPeriod(
        Period $period,
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

        if (null !== $months) {
            $actual['months'] = $period->months();
            $expected['months'] = $months;
        }

        if (null !== $days) {
            $actual['days'] = $period->days();
            $expected['days'] = $days;
        }

        if (null !== $hours) {
            $actual['hours'] = $period->hours();
            $expected['hours'] = $hours;
        }

        if (null !== $minutes) {
            $actual['minutes'] = $period->minutes();
            $expected['minutes'] = $minutes;
        }

        if (null !== $seconds) {
            $actual['seconds'] = $period->seconds();
            $expected['seconds'] = $seconds;
        }

        if (null !== $microseconds) {
            $actual['microseconds'] = $period->microseconds();
            $expected['microseconds'] = $microseconds;
        }

        static::assertSame($expected, $actual);
    }

    /**
     * @return Generator<int, array{non-empty-string}>
     */
    public static function timeZoneNames(): Generator
    {
        $timeZoneNames = [
            'UTC',
            '-12:00',
            '+00:00',
            '+14:00',
            'Pacific/Pago_Pago',
            'Europe/London',
            'Pacific/Kiritimati',
        ];

        foreach ($timeZoneNames as $timeZoneName) {
            yield [$timeZoneName];
        }
    }
}
