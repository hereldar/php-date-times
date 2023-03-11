<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Services;

use DateInterval as StandardDateInterval;
use DateTimeImmutable as StandardDateTime;
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
        StandardDateTime $stdDateTime,
        IPeriod $period,
    ): StandardDateTime {
        $periodWithoutYearsAndMonths = $period->with(0, 0);

        if ($period->months()) {
            $stdDateTime = self::addMonths($stdDateTime, $period->months());
        }

        if ($period->years()) {
            $stdDateTime = self::addYears($stdDateTime, $period->years());
        }

        if (!$periodWithoutYearsAndMonths->isZero()) {
            $stdDateTime = $stdDateTime->add(
                $periodWithoutYearsAndMonths->toStandard()
            );
        }

        return $stdDateTime;
    }

    private static function addMonths(
        StandardDateTime $stdDateTime,
        int $months,
    ): StandardDateTime {
        $mayOverflow = ($stdDateTime->format('j') > 28);

        $stdDateInterval = new StandardDateInterval('PT0S');
        $stdDateInterval->m = abs($months);
        $stdDateInterval->invert = (0 > $months) ? 1 : 0;
        $stdDateTime = $stdDateTime->add($stdDateInterval);

        if (!$mayOverflow || 28 <= $stdDateTime->format('j')) {
            return $stdDateTime;
        }

        $year = (int) $stdDateTime->format('Y');

        $month = ((int) $stdDateTime->format('m')) - 1;
        if ($month === 0) {
            --$year;
            ++$month;
        }

        $day = self::MONTH_LAST_DAY[$month];
        if ($month === 2 && $stdDateTime->format('L')) {
            ++$day;
        }

        return $stdDateTime->setDate($year, $month, $day);
    }

    private static function addYears(
        StandardDateTime $stdDateTime,
        int $years,
    ): StandardDateTime {
        $isLeapDay = ($stdDateTime->format('n-j') === '2-29');

        $stdDateInterval = new StandardDateInterval('PT0S');
        $stdDateInterval->y = abs($years);
        $stdDateInterval->invert = (0 > $years) ? 1 : 0;
        $stdDateTime = $stdDateTime->add($stdDateInterval);

        if (!$isLeapDay || $stdDateTime->format('n') !== '3') {
            return $stdDateTime;
        }

        $year = (int) $stdDateTime->format('Y');

        return $stdDateTime->setDate($year, 2, 28);
    }
}
