<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeImmutable as StandardDateTime;
use Hereldar\DateTimes\Exceptions\Overflow;
use Hereldar\Results\Interfaces\IResult;
use Stringable;

interface ILocalDate extends Stringable
{
    final public const ATOM = 'Y-m-d';
    final public const COOKIE = 'l, d-M-Y';
    final public const ISO8601 = 'Y-m-d';
    final public const RFC822 = 'D, d M y';
    final public const RFC850 = 'l, d-M-y';
    final public const RFC1036 = 'D, d M y';
    final public const RFC1123 = 'D, d M Y';
    final public const RFC2822 = 'D, d M Y';
    final public const RFC3339 = 'Y-m-d';
    final public const RFC3339_EXTENDED = 'Y-m-d';
    final public const RFC7231 = 'D, d M Y';
    final public const RSS = 'D, d M Y';
    final public const W3C = 'Y-m-d';

    public function format(string $format = self::ISO8601): string;

    public function toIso8601(): string;

    public function toRfc2822(): string;

    public function toRfc3339(): string;

    public function toStandardDateTime(): StandardDateTime;

    public function atTime(ILocalTime $time): ILocalDateTime;

    public function year(): int;

    public function month(): int;

    public function week(): int;

    public function weekYear(): int;

    /**
     * Returns the day of month.
     */
    public function day(): int;

    public function dayOfWeek(): int;

    public function dayOfYear(): int;

    public function compareTo(self $that): int;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function plus(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): static;

    public function minus(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): static;

    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
    ): static;

    /**
     * @return IResult<static, Overflow>
     */
    public function add(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): IResult;

    /**
     * @return IResult<static, Overflow>
     */
    public function subtract(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): IResult;
}
