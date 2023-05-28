<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Interfaces\Parsable;
use Hereldar\DateTimes\Interfaces\Timelike;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class LocalTime implements Timelike, Formattable, Summable, Parsable, Stringable
{
    final public const ISO8601 = 'H:i:s';
    final public const ISO8601_MILLISECONDS = 'H:i:s.v';
    final public const ISO8601_MICROSECONDS = 'H:i:s.u';

    final public const RFC2822 = 'H:i:s';

    final public const RFC3339 = 'H:i:s';
    final public const RFC3339_MILLISECONDS = 'H:i:s.v';
    final public const RFC3339_MICROSECONDS = 'H:i:s.u';

    final public const SQL = 'H:i:s';
    final public const SQL_MILLISECONDS = 'H:i:s.v';
    final public const SQL_MICROSECONDS = 'H:i:s.u';

    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function now(
        TimeZone|Offset|string $timeZone = 'UTC',
    ): static {
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof TimeZone => $timeZone->toNative(),
            $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
        };

        $dt = new NativeDateTime('now', $tz);

        if ($timeZone === 'UTC' || $tz->getName() === 'UTC') {
            return new static($dt->setDate(1970, 1, 1));
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

    public static function parse(
        string $string,
        string|array $format = LocalTime::ISO8601,
    ): Ok|Error {
        $tz = TimeZone::utc()->toNative();

        /** @var array<int, string> $formats */
        $formats = [];

        if (is_array($format)) {
            if (count($format) === 0) {
                throw new InvalidArgumentException(
                    'At least one format must be passed'
                );
            }
            $formats = $format;
            $format = reset($formats);
        }

        $result = self::parseSimple($string, $format, $tz);

        if ($result->isOk()) {
            return $result;
        }

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
                $r = self::parseSimple($string, $fmt, $tz);

                if ($r->isOk()) {
                    return $r;
                }
            }
        }

        return $result;
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    private static function parseSimple(
        string $string,
        string $format,
        NativeTimeZone $tz,
    ): Ok|Error {
        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        $info = NativeDateTime::getLastErrors();

        /** @psalm-suppress PossiblyFalseArgument */
        if (empty($info['errors']) && empty($info['warnings'])) {
            /** @var Ok<static> */
            return Ok::withValue(new static($dt));
        }

        /** @psalm-suppress PossiblyInvalidArrayAccess */
        $firstError = reset($info['errors']) ?: reset($info['warnings']) ?: null;

        return Error::withException(new ParseException($string, $format, $firstError));
    }

    public static function fromIso8601(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => self::ISO8601_MICROSECONDS,
            $milliseconds => self::ISO8601_MILLISECONDS,
            default => self::ISO8601,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, self::RFC2822)->orFail();
    }

    public static function fromRfc3339(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => self::RFC3339_MICROSECONDS,
            $milliseconds => self::RFC3339_MILLISECONDS,
            default => self::RFC3339,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromSql(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => self::SQL_MICROSECONDS,
            $milliseconds => self::SQL_MILLISECONDS,
            default => self::SQL,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('G:i:s.u');

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    public function format(string $format = LocalTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => self::ISO8601_MICROSECONDS,
            $milliseconds => self::ISO8601_MILLISECONDS,
            default => self::ISO8601,
        });
    }

    public function toRfc2822(): string
    {
        return $this->value->format(self::RFC2822);
    }

    public function toRfc3339(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => self::RFC3339_MICROSECONDS,
            $milliseconds => self::RFC3339_MILLISECONDS,
            default => self::RFC3339,
        });
    }

    public function toSql(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => self::SQL_MICROSECONDS,
            $milliseconds => self::SQL_MILLISECONDS,
            default => self::SQL,
        });
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function atDate(LocalDate $date): LocalDateTime
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
        /** @var int<0, 23> */
        return (int) $this->value->format('G');
    }

    public function minute(): int
    {
        /** @var int<0, 59> */
        return (int) $this->value->format('i');
    }

    public function second(): int
    {
        /** @var int<0, 59> */
        return (int) $this->value->format('s');
    }

    public function millisecond(): int
    {
        /** @var int<0, 999> */
        return (int) $this->value->format('v');
    }

    public function microsecond(): int
    {
        /** @var int<0, 999999> */
        return (int) $this->value->format('u');
    }

    public function compareTo(LocalTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(LocalTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(LocalTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(LocalTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(LocalTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(LocalTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(LocalTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(LocalTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(LocalTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    public function plus(
        int|Period $hours = 0,
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
        int|Period $hours = 0,
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
        int|Period $hours = 0,
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
        int|Period $hours = 0,
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
