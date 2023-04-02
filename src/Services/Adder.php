<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Services;

use DateInterval as NativeDateInterval;
use DateTimeImmutable as NativeDateTime;
use Hereldar\DateTimes\Interfaces\IPeriod;

/**
 * @internal
 */
final class Adder
{
    private const MONTH_LAST_DAY = [
        1 => 31,
        2 => 28,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31,
    ];

    public static function addPeriodWithoutOverflow(
        NativeDateTime $dateTime,
        IPeriod $period,
    ): NativeDateTime {
        $periodWithoutYearsAndMonths = $period->with(0, 0);

        if ($period->months()) {
            $dateTime = self::addMonths($dateTime, $period->months());
        }

        if ($period->years()) {
            $dateTime = self::addYears($dateTime, $period->years());
        }

        if (!$periodWithoutYearsAndMonths->isZero()) {
            $dateTime = $dateTime->add(
                $periodWithoutYearsAndMonths->toNative()
            );
        }

        return $dateTime;
    }

    private static function addMonths(
        NativeDateTime $dateTime,
        int $months,
    ): NativeDateTime {
        $mayOverflow = ($dateTime->format('j') > 28);

        $dateInterval = new NativeDateInterval('PT0S');
        $dateInterval->m = abs($months);
        $dateInterval->invert = (0 > $months) ? 1 : 0;
        $dateTime = $dateTime->add($dateInterval);

        if (!$mayOverflow || 28 <= $dateTime->format('j')) {
            return $dateTime;
        }

        $year = (int) $dateTime->format('Y');

        $month = ((int) $dateTime->format('m')) - 1;
        if ($month === 0) {
            --$year;
            ++$month;
        }

        $day = self::MONTH_LAST_DAY[$month];
        if ($month === 2 && $dateTime->format('L')) {
            ++$day;
        }

        return $dateTime->setDate($year, $month, $day);
    }

    private static function addYears(
        NativeDateTime $dateTime,
        int $years,
    ): NativeDateTime {
        $isLeapDay = ($dateTime->format('n-j') === '2-29');

        $dateInterval = new NativeDateInterval('PT0S');
        $dateInterval->y = abs($years);
        $dateInterval->invert = (0 > $years) ? 1 : 0;
        $dateTime = $dateTime->add($dateInterval);

        if (!$isLeapDay || $dateTime->format('n') !== '3') {
            return $dateTime;
        }

        $year = (int) $dateTime->format('Y');

        return $dateTime->setDate($year, 2, 28);
    }
}
