<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTime as MutableNativeDateTime;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class DateTime implements IDateTime, Stringable
{
    /** @var array<class-string, static> */
    private static array $cache = [];

    private function __construct(
        private readonly NativeDateTime $value
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function epoch(): static
    {
        $class = static::class;

        return self::$cache[$class] ??= static::of(1970, 1, 1, 0, 0, 0, 0, 'UTC');
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

        return new static($dt);
    }

    public static function of(
        int $year = 1970,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): static {
        Validator::month($month);
        Validator::day($day, $month, $year);
        Validator::hour($hour);
        Validator::minute($minute);
        Validator::second($second);
        Validator::microsecond($microsecond);

        if ($year < 0) {
            $extraYears = $year;
            $year = 0;
        } elseif ($year > 9999) {
            $extraYears = $year - 9999;
            $year = 9999;
        } else {
            $extraYears = 0;
        }

        $string = sprintf(
            '%04d-%d-%d %d:%02d:%02d.%06d',
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        $dateTime = static::parse($string, 'Y-n-j G:i:s.u', $timeZone)->orFail();

        if ($extraYears !== 0) {
            return $dateTime->plus($extraYears);
        }

        return $dateTime;
    }

    /**
     * @param string|array<int, string> $format
     *
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string|array $format = IDateTime::ISO8601,
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): Ok|Error {
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof ITimeZone => $timeZone->toNative(),
            $timeZone instanceof IOffset => $timeZone->toTimeZone()->toNative(),
        };

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

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        if (false !== $dt) {
            /** @var Ok<static> */
            return Ok::withValue(new static($dt));
        }

        $info = NativeDateTime::getLastErrors();

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
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

    public static function fromCookie(string $value): static
    {
        return static::parse($value, IDateTime::COOKIE_VARIANTS)->orFail();
    }

    public static function fromHttp(string $value): static
    {
        return static::parse($value, IDateTime::HTTP_VARIANTS)->orFail();
    }

    public static function fromIso8601(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => IDateTime::ISO8601_MICROSECONDS,
            $milliseconds => IDateTime::ISO8601_MILLISECONDS,
            default => IDateTime::ISO8601,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, IDateTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => IDateTime::RFC3339_MICROSECONDS,
            $milliseconds => IDateTime::RFC3339_MILLISECONDS,
            default => IDateTime::RFC3339,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromSql(
        string $value,
        bool $milliseconds = false,
        bool $microseconds = false,
    ): static {
        $format = match (true) {
            $microseconds => IDateTime::SQL_MICROSECONDS,
            $milliseconds => IDateTime::SQL_MILLISECONDS,
            default => IDateTime::SQL,
        };

        return static::parse($value, $format)->orFail();
    }

    public static function fromNative(NativeDateTimeInterface $value): static
    {
        if ($value instanceof MutableNativeDateTime) {
            $value = NativeDateTime::createFromMutable($value);
        }

        return new static($value);
    }

    public function format(string $format = IDateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toCookie(): string
    {
        return $this->value->format(IDateTime::COOKIE);
    }

    public function toHttp(): string
    {
        $tz = TimeZone::utc()->toNative();

        return $this->value->setTimezone($tz)->format(IDateTime::HTTP);
    }

    public function toIso8601(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => IDateTime::ISO8601_MICROSECONDS,
            $milliseconds => IDateTime::ISO8601_MILLISECONDS,
            default => IDateTime::ISO8601,
        });
    }

    public function toRfc2822(): string
    {
        return $this->value->format(IDateTime::RFC2822);
    }

    public function toRfc3339(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => IDateTime::RFC3339_MICROSECONDS,
            $milliseconds => IDateTime::RFC3339_MILLISECONDS,
            default => IDateTime::RFC3339,
        });
    }

    public function toSql(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->value->format(match (true) {
            $microseconds => IDateTime::SQL_MICROSECONDS,
            $milliseconds => IDateTime::SQL_MILLISECONDS,
            default => IDateTime::SQL,
        });
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function timestamp(): int
    {
        return $this->value->getTimestamp();
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

    public function inLeapYear(): bool
    {
        return ($this->value->format('L') === '1');
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

    public function offset(): IOffset
    {
        return Offset::fromTotalSeconds(
            $this->value->getOffset()
        );
    }

    public function timeZone(): ITimeZone
    {
        return TimeZone::fromNative(
            $this->value->getTimezone()
        );
    }

    public function inDaylightSavingTime(): bool
    {
        return ($this->value->format('I') === '1');
    }

    public function compareTo(IDateTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(IDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(IDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(IDateTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(IDateTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(IDateTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(IDateTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(IDateTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(IDateTime $that): bool
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
        ITimeZone|IOffset|string|null $timeZone = null,
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

        if ($timeZone !== null) {
            $dt = $dt->setTimezone(match (true) {
                is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
                $timeZone instanceof ITimeZone => $timeZone->toNative(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toNative(),
            });
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
