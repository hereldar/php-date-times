<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Services;

use OutOfRangeException;

final class Validator
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

    public static function range(string $param, int $value, int $min, int $max): void
    {
        if ($value < $min || $value > $max) {
            throw new OutOfRangeException("{$param} must be between {$min} and {$max}, {$value} given");
        }
    }

    public static function month(int $value): void
    {
        self::range('month', $value, 1, 12);
    }

    public static function day(int $day, int $month, int $year): void
    {
        if (2 === $month && self::yearIsLeap($year)) {
            $max = 29;
        } else {
            $max = self::MONTH_LAST_DAY[$month] ?? 31;
        }

        self::range('day', $day, 1, $max);
    }

    public static function dayOfYear(int $day, int $year): void
    {
        if (self::yearIsLeap($year)) {
            $max = 366;
        } else {
            $max = 365;
        }

        self::range('day', $day, 1, $max);
    }

    public static function hour(int $value): void
    {
        self::range('hour', $value, 0, 23);
    }

    public static function minute(int $value): void
    {
        self::range('minute', $value, 0, 59);
    }

    public static function second(int $value): void
    {
        self::range('second', $value, 0, 59);
    }

    public static function microsecond(int $value): void
    {
        self::range('microsecond', $value, 0, 999_999);
    }

    private static function yearIsLeap(int $year): bool
    {
        if (0 !== $year % 4) {
            return false;
        }

        return !(0 === $year % 100 && 0 !== $year % 400)

        ;
    }
}
