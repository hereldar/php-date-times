<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeImmutable as StandardDateTime;
use Hereldar\DateTimes\Exceptions\Overflow;
use Hereldar\Results\Interfaces\IResult;
use Stringable;

interface ILocalTime extends Stringable
{
    final public const ATOM = 'H:i:s';
    final public const COOKIE = 'H:i:s';
    final public const ISO8601 = 'H:i:s';
    final public const RFC822 = 'H:i:s';
    final public const RFC850 = 'H:i:s';
    final public const RFC1036 = 'H:i:s';
    final public const RFC1123 = ' H:i:s';
    final public const RFC2822 = 'H:i:s';
    final public const RFC3339 = 'H:i:s';
    final public const RFC3339_EXTENDED = 'H:i:s.v';
    final public const RFC7231 = 'H:i:s';
    final public const RSS = 'H:i:s';
    final public const W3C = 'H:i:s';

    public function format(string $format = self::ISO8601): string;

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

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function plus(
        ?IPeriod $period = null,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static;

    public function minus(
        ?IPeriod $period = null,
        int $hours = 0,
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
     * @return IResult<static, Overflow>
     */
    public function add(
        ?IPeriod $period = null,
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
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): IResult;
}
