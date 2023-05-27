<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTime as MutableNativeDateTime;
use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use DateTimeInterface as NativeDateTimeInterface;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\Datelike;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Interfaces\Parsable;
use Hereldar\DateTimes\Interfaces\Timelike;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class DateTime implements Datelike, Timelike, Formattable, Summable, Parsable, Stringable
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
        TimeZone|Offset|string $timeZone = 'UTC',
    ): static {
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof TimeZone => $timeZone->toNative(),
            $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
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
        TimeZone|Offset|string $timeZone = 'UTC',
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

    public static function parse(
        string $string,
        string|array $format = DateTime::ISO8601,
        TimeZone|Offset|string $timeZone = 'UTC',
    ): Ok|Error {
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof TimeZone => $timeZone->toNative(),
            $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
        };

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

    public static function fromCookie(string $value): static
    {
        return static::parse($value, self::COOKIE_VARIANTS)->orFail();
    }

    public static function fromHttp(string $value): static
    {
        return static::parse($value, self::HTTP_VARIANTS)->orFail();
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

    public static function fromNative(NativeDateTimeInterface $value): static
    {
        if ($value instanceof MutableNativeDateTime) {
            $value = NativeDateTime::createFromMutable($value);
        }

        return new static($value);
    }

    public function format(string $format = DateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toCookie(): string
    {
        return $this->value->format(self::COOKIE);
    }

    public function toHttp(): string
    {
        $tz = TimeZone::utc()->toNative();

        return $this->value->setTimezone($tz)->format(self::HTTP);
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

    public function timestamp(): int
    {
        return $this->value->getTimestamp();
    }

    /**
     * Returns the `LocalDate` part of this date-time.
     */
    public function date(): LocalDate
    {
        return LocalDate::fromNative($this->value);
    }

    public function year(): int
    {
        return (int) $this->value->format('Y');
    }

    public function month(): int
    {
        /** @var int<1, 12> */
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
        /** @var int<1, 31> */
        return (int) $this->value->format('j');
    }

    public function dayOfWeek(): int
    {
        /** @var int<1, 7> */
        return (int) $this->value->format('N');
    }

    public function dayOfYear(): int
    {
        /** @var int<1, 366> */
        return (int) $this->value->format('z') + 1;
    }

    public function inLeapYear(): bool
    {
        return ($this->value->format('L') === '1');
    }

    /**
     * Returns the `LocalTime` part of this date-time.
     */
    public function time(): LocalTime
    {
        return LocalTime::fromNative($this->value);
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

    public function offset(): Offset
    {
        return Offset::fromTotalSeconds(
            $this->value->getOffset()
        );
    }

    public function timeZone(): TimeZone
    {
        return TimeZone::fromNative(
            $this->value->getTimezone()
        );
    }

    public function inDaylightSavingTime(): bool
    {
        return ($this->value->format('I') === '1');
    }

    public function compareTo(DateTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(DateTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(DateTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(DateTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(DateTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(DateTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(DateTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(DateTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(DateTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    public function plus(
        int|Period $years = 0,
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
        int|Period $years = 0,
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
        TimeZone|Offset|string|null $timeZone = null,
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
                $timeZone instanceof TimeZone => $timeZone->toNative(),
                $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
            });
        }

        return new static($dt);
    }

    public function add(
        int|Period $years = 0,
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
        int|Period $years = 0,
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
