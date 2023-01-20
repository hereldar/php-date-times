<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests;

use Hereldar\DateTimes\Interfaces\IPeriod;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\ExceptionCode;
use PHPUnit\Framework\Constraint\ExceptionMessage;
use PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string<Throwable> $expectedException
     */
    public static function assertException(
        string $expectedException,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
            static::assertThat(
                $exception,
                new ExceptionConstraint(
                    $expectedException
                )
            );

            return;
        }

        static::assertThat(
            null,
            new ExceptionConstraint(
                $expectedException
            )
        );
    }

    public static function assertExceptionCode(
        int|string $expectedCode,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
        }

        static::assertThat(
            $exception ?? null,
            new ExceptionCode(
                $expectedCode
            )
        );
    }

    public static function assertExceptionMessage(
        string $expectedMessage,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
            return;
        }

        static::assertThat(
            $exception ?? null,
            new ExceptionMessage(
                $expectedMessage
            )
        );
    }

    public static function assertExceptionMessageMatches(
        string $regularExpression,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
        }

        static::assertThat(
            $exception ?? null,
            new ExceptionMessageRegularExpression(
                $regularExpression
            )
        );
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
