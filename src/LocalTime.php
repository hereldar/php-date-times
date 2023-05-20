<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

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
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof ITimeZone => $timeZone->toNative(),
            $timeZone instanceof IOffset => $timeZone->toTimeZone()->toNative(),
        };

        $dt = new NativeDateTime('now', $tz);

        if ($timeZone === 'UTC' || $tz->getName() === 'UTC') {
            return new static($dt);
        }

        $string = $dt->format('G:i:s.u');

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    public static function of(
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): static {
        Validator::hour($hour);
        Validator::minute($minute);
        Validator::second($second);
        Validator::microsecond($microsecond);

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
     * @param string|array<int, string> $format
     *
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string|array $format = ILocalTime::ISO8601,
    ): Ok|Error {
        $tz = TimeZone::utc()->toNative();

        /** @var array<int, string> $formats */
        $formats = [];

        if (!$format) {
            throw new InvalidArgumentException(
                'At least one format must be passed'
            );
        }

        if (is_array($format)) {
            $formats = $format;
            $format = reset($formats);
        }

        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        if (false !== $dt) {
            /** @var Ok<static> */
            return Ok::withValue(new static($dt));
        }

        $info = NativeDateTime::getLastErrors();

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
                if (!str_starts_with($fmt, '!')) {
                    $fmt = "!{$fmt}";
                }

                $dt = NativeDateTime::createFromFormat($fmt, $string, $tz);

                if (false !== $dt) {
                    /** @var Ok<static> */
                    return Ok::withValue(new static($dt));
                }
            }
        }

        $firstError = ($info)
            ? (reset($info['errors']) ?: reset($info['warnings']) ?: null)
            : null;

        return Error::withException(new ParseException($string, $format, $firstError));
    }

    public static function fromIso8601(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => ILocalTime::ISO8601_MICROSECONDS,
            $milliseconds => ILocalTime::ISO8601_MILLISECONDS,
            default => ILocalTime::ISO8601,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => ILocalTime::RFC3339_MICROSECONDS,
            $milliseconds => ILocalTime::RFC3339_MILLISECONDS,
            default => ILocalTime::RFC3339,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromSql(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
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

    public function toIso8601(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => ILocalTime::ISO8601_MICROSECONDS,
            $milliseconds => ILocalTime::ISO8601_MILLISECONDS,
            default => ILocalTime::ISO8601,
        });
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalTime::RFC2822);
    }

    public function toRfc3339(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => ILocalTime::RFC3339_MICROSECONDS,
            $milliseconds => ILocalTime::RFC3339_MILLISECONDS,
            default => ILocalTime::RFC3339,
        });
    }

    public function toSql(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
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
