<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;

interface IDateTime
{
    final public const COOKIE_VARIANTS = [
        'D, d M Y H:i:s T',
        'l, d-M-y H:i:s T',
        'l, d-M-Y H:i:s T',
        'D M j G:i:s Y',
        'D M d H:i:s Y T',
    ];
    final public const COOKIE = self::COOKIE_VARIANTS[0];

    final public const HTTP_VARIANTS = [
        'D, d M Y H:i:s \G\M\T',
        'l, d-M-y H:i:s \G\M\T',
        'l, d-M-Y H:i:s \G\M\T',
        'D M j G:i:s Y',
        'D M j H:i:s Y \G\M\T',
    ];
    final public const HTTP = self::HTTP_VARIANTS[0];

    final public const ISO8601 = 'Y-m-d\TH:i:sp';
    final public const ISO8601_MILLISECONDS = 'Y-m-d\TH:i:s.vp';
    final public const ISO8601_MICROSECONDS = 'Y-m-d\TH:i:s.up';

    final public const RFC2822 = 'D, d M Y H:i:s O';

    final public const RFC3339 = 'Y-m-d\TH:i:sP';
    final public const RFC3339_MILLISECONDS = 'Y-m-d\TH:i:s.vP';
    final public const RFC3339_MICROSECONDS = 'Y-m-d\TH:i:s.uP';

    final public const SQL = 'Y-m-d H:i:sP';
    final public const SQL_MILLISECONDS = 'Y-m-d H:i:s.vP';
    final public const SQL_MICROSECONDS = 'Y-m-d H:i:s.uP';

    /**
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = self::ISO8601): Ok|Error;

    public function toCookie(): string;

    public function toHttp(): string;

    public function toIso8601(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string;

    public function toRfc2822(): string;

    public function toRfc3339(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string;

    public function toSql(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string;

    public function toNative(): NativeDateTime;

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
        ITimeZone|IOffset|string|null $timeZone = null,
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
