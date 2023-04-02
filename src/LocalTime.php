<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

/**
 * @psalm-consistent-constructor
 */
class LocalTime implements ILocalTime, Stringable
{
    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function now(
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): static {
        try {
            $tz = match (true) {
                is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
                $timeZone instanceof ITimeZone => $timeZone->toNative(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toNative(),
            };

            $dt = new NativeDateTime('now', $tz);
        } catch (Throwable $e) {
            throw new UnexpectedValueException(
                message: get_debug_type($timeZone),
                previous: $e
            );
        }

        return new static($dt);
    }

    public static function of(
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): static {
        $string = sprintf(
            '%d:%02d:%02d.%06d',
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string $format = ILocalTime::ISO8601,
    ): Ok|Error {
        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $tz = new NativeTimeZone('UTC');

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        if (false === $dt) {
            $info = NativeDateTime::getLastErrors();
            $firstError = ($info)
                ? (reset($info['errors']) ?: reset($info['warnings']) ?: null)
                : null;

            return Error::withException(new ParseException($string, $format, $firstError));
        }

        /** @var Ok<static> */
        return Ok::withValue(new static($dt));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, ILocalTime::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        $format = ($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339;

        return static::parse($value, $format)->orFail();
    }

    public static function fromSql(string $value, bool $milliseconds = false, bool $microseconds = false): static
    {
        $format = match (true) {
            $microseconds => ILocalTime::SQL_MICROSECONDS,
            $milliseconds => ILocalTime::SQL_MILLISECONDS,
            default => ILocalTime::SQL,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('G:i:s.u');

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    public function format(string $format = ILocalTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(ILocalTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->value->format(($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339);
    }

    public function toSql(bool $milliseconds = false, bool $microseconds = false): string
    {
        return $this->value->format(match (true) {
            $microseconds => ILocalTime::SQL_MICROSECONDS,
            $milliseconds => ILocalTime::SQL_MILLISECONDS,
            default => ILocalTime::SQL,
        });
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function atDate(ILocalDate $date): ILocalDateTime
    {
        $dt = $this->value->setDate(
            $date->year(),
            $date->month(),
            $date->day(),
        );

        return LocalDateTime::fromNative($dt);
    }

    public function hour(): int
    {
        return (int) $this->value->format('G');
    }

    public function minute(): int
    {
        return (int) $this->value->format('i');
    }

    public function second(): int
    {
        return (int) $this->value->format('s');
    }

    public function millisecond(): int
    {
        return (int) $this->value->format('v');
    }

    public function microsecond(): int
    {
        return (int) $this->value->format('u');
    }

    public function compareTo(ILocalTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(ILocalTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(ILocalTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(ILocalTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(ILocalTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(ILocalTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(ILocalTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(ILocalTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(ILocalTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    public function plus(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        if (is_int($hours)) {
            $period = Period::of(
                0, 0, 0, 0,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } elseif (!$minutes && !$seconds && !$milliseconds && !$microseconds) {
            $period = $hours;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = $this->value->add($period->toNative());

        return new static($value);
    }

    public function minus(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        if (is_int($hours)) {
            $period = Period::of(
                0, 0, 0, 0,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } elseif (!$minutes && !$seconds && !$milliseconds && !$microseconds) {
            $period = $hours;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = $this->value->sub($period->toNative());

        return new static($value);
    }

    public function with(
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static {
        return new static($this->value->setTime(
            $hour ?? $this->hour(),
            $minute ?? $this->minute(),
            $second ?? $this->second(),
            $microsecond ?? $this->microsecond(),
        ));
    }

    public function add(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): Ok|Error {
        try {
            $dateTime = $this->plus(
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }

    public function subtract(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): Ok|Error {
        try {
            $dateTime = $this->minus(
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
