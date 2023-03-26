<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use DateTimeImmutable as StandardDateTime;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;

interface ILocalTime
{
    final public const ATOM = 'H:i:s';
    final public const COOKIE = 'H:i:s';
    final public const ISO8601 = 'H:i:s';
    final public const RFC822 = 'H:i:s';
    final public const RFC850 = 'H:i:s';
    final public const RFC1036 = 'H:i:s';
    final public const RFC1123 = 'H:i:s';
    final public const RFC2822 = 'H:i:s';
    final public const RFC3339 = 'H:i:s';
    final public const RFC3339_EXTENDED = 'H:i:s.v';
    final public const RFC7231 = 'H:i:s';
    final public const RSS = 'H:i:s';
    final public const W3C = 'H:i:s';

    /**
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = self::ISO8601): Ok|Error;

    public function toIso8601(): string;

    public function toRfc2822(): string;

    public function toRfc3339(bool $milliseconds = false): string;

    public function toStandard(): StandardDateTime;

    public function atDate(ILocalDate $date): ILocalDateTime;

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
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function minus(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function with(
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static;

    /**
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function add(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): Ok|Error;

    /**
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function subtract(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): Ok|Error;
}
