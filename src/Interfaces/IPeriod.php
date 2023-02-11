<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateInterval as StandardDateInterval;
use Hereldar\DateTimes\Exceptions\Overflow;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\Results\Interfaces\IResult;
use Stringable;

interface IPeriod extends Stringable
{
    final public const ISO8601 = 'P%yY%mM%dDT%hH%iM%s%fS';

    /**
     * @return IResult<string, ParseException>
     */
    public function format(string $format = self::ISO8601): IResult;

    public function toIso8601(): string;

    public function toStandardDateInterval(): StandardDateInterval;

    public function years(): int;

    public function months(): int;

    public function days(): int;

    public function hours(): int;

    public function minutes(): int;

    public function seconds(): int;

    public function microseconds(): int;

    public function compareTo(self $that): int;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function isNegative(): bool;

    public function isZero(): bool;

    public function plus(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function minus(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function multipliedBy(int $multiplicand): static;

    public function dividedBy(int $divisor): static;

    public function abs(): static;

    public function negated(): static;

    public function with(
        ?int $years = null,
        ?int $months = null,
        ?int $days = null,
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
        ?int $microseconds = null,
    ): static;

    /**
     * @return IResult<static, Overflow>
     */
    public function add(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): IResult;

    /**
     * @return IResult<static, Overflow>
     */
    public function subtract(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): IResult;

    /**
     * @return IResult<static, Overflow>
     */
    public function multiplyBy(int $multiplicand): IResult;

    /**
     * @return IResult<static, Overflow>
     */
    public function divideBy(int $divisor): IResult;
}
