<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeImmutable as StandardDateTime;
use Hereldar\DateTimes\Exceptions\Overflow;
use Hereldar\Results\Interfaces\IResult;
use Stringable;

interface IDateTime extends Stringable
{
    final public const ATOM = 'Y-m-d\TH:i:sP';
    final public const COOKIE = 'l, d-M-Y H:i:s T';
    final public const ISO8601 = 'Y-m-d\TH:i:sP';
    final public const RFC822 = 'D, d M y H:i:s O';
    final public const RFC850 = 'l, d-M-y H:i:s T';
    final public const RFC1036 = 'D, d M y H:i:s O';
    final public const RFC1123 = 'D, d M Y H:i:s O';
    final public const RFC2822 = 'D, d M Y H:i:s O';
    final public const RFC3339 = 'Y-m-d\TH:i:sP';
    final public const RFC3339_EXTENDED = 'Y-m-d\TH:i:s.vP';
    final public const RFC7231 = 'D, d M Y H:i:s \G\M\T';
    final public const RSS = 'D, d M Y H:i:s O';
    final public const W3C = 'Y-m-d\TH:i:sP';

    public function format(string $format = self::ISO8601): string;

    public function toIso8601(): string;

    public function toRfc2822(): string;

    public function toRfc3339(bool $milliseconds = false): string;

    public function toStandardDateTime(): StandardDateTime;

    public function timestamp(): int;

    /**
     * Returns the `LocalDate` part of this date-time.
     */
    public function date(): ILocalDate;

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

    /**
     * Returns the `LocalTime` part of this date-time.
     */
    public function time(): ILocalTime;

    public function hour(): int;

    public function minute(): int;

    public function second(): int;

    public function millisecond(): int;

    public function microsecond(): int;

    public function offset(): IOffset;

    public function timezone(): ITimeZone;

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
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function minus(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
        ITimeZone|IOffset|string|null $timeZone = null,
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
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): IResult;
}
