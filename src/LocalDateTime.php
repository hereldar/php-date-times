<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

/**
 * @psalm-consistent-constructor
 */
class LocalDateTime implements ILocalDateTime, Stringable
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
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): static {
        $string = sprintf(
            '%d-%d-%d %d:%02d:%02d.%06d',
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        return static::parse($string, 'Y-n-j G:i:s.u')->orFail();
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string $format = ILocalDateTime::ISO8601,
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
        return static::parse($value, ILocalDateTime::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalDateTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        $format = ($milliseconds)
            ? ILocalDateTime::RFC3339_EXTENDED
            : ILocalDateTime::RFC3339;

        return static::parse($value, $format)->orFail();
    }

    public static function fromSql(string $value, bool $milliseconds = false, bool $microseconds = false): static
    {
        $format = match (true) {
            $microseconds => ILocalDateTime::SQL_MICROSECONDS,
            $milliseconds => ILocalDateTime::SQL_MILLISECONDS,
            default => ILocalDateTime::SQL,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromNative(NativeDateTimeInterface $value): static
    {
        $string = $value->format('Y-n-j G:i:s.u');

        return static::parse($string, 'Y-n-j G:i:s.u')->orFail();
    }

    public function format(string $format = ILocalDateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(ILocalDateTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalDateTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->value->format(($milliseconds)
            ? ILocalDateTime::RFC3339_EXTENDED
            : ILocalDateTime::RFC3339);
    }

    public function toSql(bool $milliseconds = false, bool $microseconds = false): string
    {
        return $this->value->format(match (true) {
            $microseconds => ILocalDateTime::SQL_MICROSECONDS,
            $milliseconds => ILocalDateTime::SQL_MILLISECONDS,
            default => ILocalDateTime::SQL,
        });
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function atTimeZone(ITimeZone $timeZone): IDateTime
    {
        return DateTime::parse(
            $this->value->format('Y-n-j G:i:s.u'),
            'Y-n-j G:i:s.u',
            $timeZone
        )->orFail();
    }

    public function atOffset(IOffset $offset): IDateTime
    {
        return DateTime::parse(
            $this->value->format('Y-n-j G:i:s.u'),
            'Y-n-j G:i:s.u',
            $offset
        )->orFail();
    }

    public function date(): ILocalDate
    {
        return LocalDate::fromNative($this->value);
    }

    public function year(): int
    {
        return (int) $this->value->format('Y');
    }

    public function month(): int
    {
        return (int) $this->value->format('n');
    }

    public function week(): int
    {
        return (int) $this->value->format('W');
    }

    public function weekYear(): int
    {
        return (int) $this->value->format('o');
    }

    public function day(): int
    {
        return (int) $this->value->format('j');
    }

    public function dayOfWeek(): int
    {
        return (int) $this->value->format('N');
    }

    public function dayOfYear(): int
    {
        return (int) $this->value->format('z') + 1;
    }

    public function time(): ILocalTime
    {
        return LocalTime::fromNative($this->value);
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

    public function compareTo(ILocalDateTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(ILocalDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(ILocalDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(ILocalDateTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(ILocalDateTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(ILocalDateTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(ILocalDateTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(ILocalDateTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(ILocalDateTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

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
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } elseif (!$months && !$weeks && !$days
            && !$hours && !$minutes && !$seconds
            && !$milliseconds && !$microseconds) {
            $period = $years;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = (!$overflow && ($period->months() || $period->years()))
            ? Adder::addPeriodWithoutOverflow($this->value, $period)
            : $this->value->add($period->toNative());

        return new static($value);
    }

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
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } elseif (!$months && !$weeks && !$days
            && !$hours && !$minutes && !$seconds
            && !$milliseconds && !$microseconds) {
            $period = $years;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = (!$overflow && ($period->months() || $period->years()))
            ? Adder::addPeriodWithoutOverflow($this->value, $period->negated())
            : $this->value->sub($period->toNative());

        return new static($value);
    }

    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static {
        $dt = $this->value;

        if ($year !== null
            || $month !== null
            || $day !== null) {
            $dt = $dt->setDate(
                $year ?? $this->year(),
                $month ?? $this->month(),
                $day ?? $this->day(),
            );
        }

        if ($hour !== null
            || $minute !== null
            || $second !== null
            || $microsecond !== null) {
            $dt = $dt->setTime(
                $hour ?? $this->hour(),
                $minute ?? $this->minute(),
                $second ?? $this->second(),
                $microsecond ?? $this->microsecond(),
            );
        }

        return new static($dt);
    }

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
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->plus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
                $overflow,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }

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
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->minus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
                $overflow,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
