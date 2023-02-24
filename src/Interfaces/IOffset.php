<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\Results\Interfaces\IResult;
use InvalidArgumentException;
use Stringable;

interface IOffset extends Stringable
{
    final public const ISO8601 = '%R%H:%I'; // +02:00

    /**
     * @return IResult<string, InvalidArgumentException>
     */
    public function format(string $format = self::ISO8601): IResult;

    public function toIso8601(bool $seconds = false): string;

    public function hours(): int;

    public function minutes(): int;

    public function seconds(): int;

    public function totalMinutes(): int;

    public function totalSeconds(): int;

    public function compareTo(self $that): int;

    public function is(self $that): bool;

    public function isNot(self $that): bool;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function isNegative(): bool;

    public function isPositive(): bool;

    public function isZero(): bool;

    public function plus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static;

    public function minus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static;

    public function abs(): static;

    public function negated(): static;

    public function with(
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
    ): static;

    public function add(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): IResult;

    public function subtract(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): IResult;
}
