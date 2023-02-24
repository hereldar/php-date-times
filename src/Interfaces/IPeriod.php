<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use DateInterval as StandardDateInterval;
use Hereldar\Results\Interfaces\IResult;
use InvalidArgumentException;

interface IPeriod
{
    final public const ISO8601 = 'P%yY%mM%dDT%hH%iM%s%fS';

    /**
     * @return IResult<string, InvalidArgumentException>
     */
    public function format(string $format = self::ISO8601): IResult;

    public function toIso8601(): string;

    public function toStandard(): StandardDateInterval;

    public function years(): int;

    public function months(): int;

    public function days(): int;

    public function hours(): int;

    public function minutes(): int;

    public function seconds(): int;

    public function microseconds(): int;

    public function compareTo(self $that): int;

    public function is(self $that): bool;

    public function isNot(self $that): bool;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isSimilar(self $that): bool;

    public function isNotSimilar(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function hasNegativeValues(): bool;

    public function hasPositiveValues(): bool;

    public function isNegative(): bool;

    public function isPositive(): bool;

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

    public function normalized(): static;

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
     * @return IResult<static, ArithmeticError>
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
     * @return IResult<static, ArithmeticError>
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
     * @return IResult<static, ArithmeticError>
     */
    public function multiplyBy(int $multiplicand): IResult;

    /**
     * @return IResult<static, ArithmeticError>
     */
    public function divideBy(int $divisor): IResult;
}
