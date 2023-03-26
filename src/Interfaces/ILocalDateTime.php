<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use DateTimeImmutable as StandardDateTime;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;

interface ILocalDateTime
{
    final public const ATOM = 'Y-m-d\TH:i:s';
    final public const COOKIE = 'l, d-M-Y H:i:s';
    final public const ISO8601 = 'Y-m-d\TH:i:s';
    final public const RFC822 = 'D, d M y H:i:s';
    final public const RFC850 = 'l, d-M-y H:i:s';
    final public const RFC1036 = 'D, d M y H:i:s';
    final public const RFC1123 = 'D, d M Y H:i:s';
    final public const RFC2822 = 'D, d M Y H:i:s';
    final public const RFC3339 = 'Y-m-d\TH:i:s';
    final public const RFC3339_EXTENDED = 'Y-m-d\TH:i:s.v';
    final public const RFC7231 = 'D, d M Y H:i:s';
    final public const RSS = 'D, d M Y H:i:s';
    final public const W3C = 'Y-m-d\TH:i:s';

    /**
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = self::ISO8601): Ok|Error;

    public function toIso8601(): string;

    public function toRfc2822(): string;

    public function toRfc3339(bool $milliseconds = false): string;

    public function toStandard(): StandardDateTime;

    public function atTimeZone(ITimeZone $timeZone): IDateTime;

    public function atOffset(IOffset $offset): IDateTime;

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

    public function compareTo(self $that): int;

    public function is(self $that): bool;

    public function isNot(self $that): bool;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

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

    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static;

    /**
     * @return Ok<static>|Error<ArithmeticError>
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
    ): Ok|Error;

    /**
     * @return Ok<static>|Error<ArithmeticError>
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
    ): Ok|Error;
}
