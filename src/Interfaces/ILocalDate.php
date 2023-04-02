<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;

interface ILocalDate
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
    final public const SQL = 'Y-m-d';
    final public const W3C = 'Y-m-d';

    /**
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = self::ISO8601): Ok|Error;

    public function toIso8601(): string;

    public function toRfc2822(): string;

    public function toRfc3339(): string;

    public function toSql(): string;

    public function toNative(): NativeDateTime;

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
    ): static;

    public function minus(
        int|IPeriod $years = 0,
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
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function add(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): Ok|Error;

    /**
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function subtract(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
    ): Ok|Error;
}
