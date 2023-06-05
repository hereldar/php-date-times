<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Interfaces\Parsable;
use Hereldar\DateTimes\Interfaces\Timelike;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use OutOfRangeException;
use Stringable;

/**
 * A time without a time-zone in the ISO-8601 calendar system, such as
 * 17:30:09.
 *
 * This class does not store a date or time-zone.  Instead, it is a
 * description of the local time as seen on a wall clock. It cannot
 * represent an instant on the time-line without additional
 * information such as an offset or time-zone.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
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

    /** @var array<class-string, static> */
    private static array $maximums = [];

    /** @var array<class-string, static> */
    private static array $minimums = [];

    /** @var array<class-string, static> */
    private static array $noons = [];

    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    /**
     * Outputs this time as a `string`, using the default format of
     * the class.
     */
    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    /**
     * The Unix epoch (00:00:00).
     */
    public static function epoch(): static
    {
        return self::$minimums[static::class] ??= static::of(0, 0, 0, 0);
    }

    /**
     * The maximum supported time (23:59:59.999999).
     */
    public static function max(): static
    {
        return self::$maximums[static::class] ??= static::of(23, 59, 59, 999_999);
    }

    /**
     * The minimum supported time (00:00:00).
     */
    public static function min(): static
    {
        return self::$minimums[static::class] ??= static::of(0, 0, 0, 0);
    }

    /**
     * The time of midnight at the start of the day (00:00:00).
     */
    public static function midnight(): static
    {
        return self::$minimums[static::class] ??= static::of(0, 0, 0, 0);
    }

    /**
     * The time of noon in the middle of the day (12:00:00).
     */
    public static function noon(): static
    {
        return self::$noons[static::class] ??= static::of(12, 0, 0, 0);
    }

    /**
     * Obtains the current time from the system clock in the specified
     * time-zone. If no time-zone is specified, the `UTC` time-zone
     * will be used.
     *
     * @throws TimeZoneException if the time-zone name cannot be found
     */
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

    /**
     * Makes a new `LocalTime` with the specified hour, minute, second
     * and microsecond. The time units must be within their valid
     * range, otherwise an exception will be thrown.
     *
     * All parameters are optional and, if not specified, will take
     * their Unix epoch value (00:00:00).
     *
     * @param int<0, 23> $hour
     * @param int<0, 59> $minute
     * @param int<0, 59> $second
     * @param int<0, 999999> $microsecond
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
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
     * Makes a new `LocalTime` from a text string using a specific
     * format. It also accepts a list of formats.
     *
     * If the format is not specified, the ISO 8601 time format will
     * be used (`H:i:s`).
     *
     * The `LocalTime` is not returned directly, but a result that
     * will contain the time if no error was found, or an exception if
     * something went wrong.
     *
     * @param string|array<int, string> $format
     *
     * @throws InvalidArgumentException if an empty list of formats is passed
     *
     * @return Ok<static>|Error<ParseException>
     */
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

    /**
     * Makes a new `LocalTime` from a text with the ISO 8601 time
     * format (e.g. `'17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
     * setting respectively `$milliseconds` or `$microseconds` to true.
     *
     * The time is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
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

    /**
     * Makes a new `LocalTime` from a text with the RFC 2822 time
     * format (e.g. `'17:30:09'`).
     *
     * The time is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, self::RFC2822)->orFail();
    }

    /**
     * Makes a new `LocalTime` from a text with the RFC 3339 time
     * format (e.g. `'17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
     * setting respectively `$milliseconds` or `$microseconds` to true.
     *
     * The time is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
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

    /**
     * Makes a new `LocalTime` from a text with the SQL time format
     * (e.g. `'17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
     * setting respectively `$milliseconds` or `$microseconds` to true.
     *
     * The time is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
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

    /**
     * Makes a new `LocalTime` from a native `DateTime` or
     * `DateTimeImmutable`.
     *
     * Only the time values will be taken, while date and time-zone
     * values will be ignored.
     */
    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('G:i:s.u');

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    /**
     * Formats this time using the specified format.
     *
     * If the format is not specified, the ISO 8601 time format will
     * be used (`H:i:s`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = LocalTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    /**
     * Formats this time with the ISO 8601 time format (e.g.
     * `'17:30:09'`).
     *
     * It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
     * microseconds (e.g. `'17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
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

    /**
     * Formats this time with the RFC 2822 time format (e.g.
     * `'17:30:09'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc2822(): string
    {
        return $this->value->format(self::RFC2822);
    }

    /**
     * Formats this time with the RFC 3339 time format (e.g.
     * `'17:30:09'`).
     *
     * It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
     * microseconds (e.g. `'17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
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

    /**
     * Formats this time with the SQL time format (e.g. `'17:30:09'`).
     *
     * It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
     * microseconds (e.g. `'17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
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

    /**
     * Returns a native `DateTimeImmutable` with the values of this
     * time.
     *
     * The date and time-zone values will be taken from the Unix epoch
     * (1 January 1970 UTC).
     */
    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    /**
     * Combines this time with a date to make a `LocalDateTime`. It
     * accepts a `LocalDate` or individual time units.
     *
     * If a `LocalDate` is passed as the first argument, no further
     * arguments will be accepted.
     *
     * If individual time units are passed, they must be within their
     * valid range. Missing units will be taken from the Unix epoch
     * (1 January 1970).
     *
     * @throws InvalidArgumentException if a `LocalDate` is combined with some time units
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function atDate(
        LocalDate|int $year = 1970,
        int $month = 1,
        int $day = 1,
    ): LocalDateTime {
        if (is_int($year)) {
            Validator::month($month);
            Validator::day($day, $month, $year);
        } elseif ($month === 1 && $day === 1) {
            $date = $year;
            $year = $date->year();
            $month = $date->month();
            $day = $date->day();
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a local date is passed'
            );
        }

        $dt = $this->value->setDate(
            $year,
            $month,
            $day,
        );

        return LocalDateTime::fromNative($dt);
    }

    /**
     * Returns the hour of the day.
     *
     * @return int<0, 23>
     */
    public function hour(): int
    {
        /** @var int<0, 23> */
        return (int) $this->value->format('G');
    }

    /**
     * Returns the minute of the hour.
     *
     * @return int<0, 59>
     */
    public function minute(): int
    {
        /** @var int<0, 59> */
        return (int) $this->value->format('i');
    }

    /**
     * Returns the second of the minute.
     *
     * @return int<0, 59>
     */
    public function second(): int
    {
        /** @var int<0, 59> */
        return (int) $this->value->format('s');
    }

    /**
     * Returns the millisecond of the second.
     *
     * @return int<0, 999>
     */
    public function millisecond(): int
    {
        /** @var int<0, 999> */
        return (int) $this->value->format('v');
    }

    /**
     * Returns the microsecond of the second.
     *
     * @return int<0, 999999>
     */
    public function microsecond(): int
    {
        /** @var int<0, 999999> */
        return (int) $this->value->format('u');
    }

    /**
     * Compares this time to another time.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * time is before, equal to, or after the given time.
     */
    public function compareTo(LocalTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    /**
     * Checks if the given time belongs to the same class and has the
     * same value as this time.
     */
    public function is(LocalTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    /**
     * Checks if the given time belongs to another class and has a
     * different value than this time.
     */
    public function isNot(LocalTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    /**
     * Checks if the given time has the same value as this time.
     */
    public function isEqual(LocalTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    /**
     * Checks if the given time has a different value from this time.
     */
    public function isNotEqual(LocalTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    /**
     * Checks if this time is after the specified time.
     */
    public function isGreater(LocalTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    /**
     * Checks if this time is after or equal to the specified time.
     */
    public function isGreaterOrEqual(LocalTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    /**
     * Checks if this time is before the specified time.
     */
    public function isLess(LocalTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    /**
     * Checks if this time is before or equal to the specified time.
     */
    public function isLessOrEqual(LocalTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    /**
     * Returns a copy of this time with the specified amount of hours,
     * minutes, seconds and microseconds added. It accepts a `Period`
     * or individual time units.
     *
     * If a `Period` is passed as the first argument, no individual
     * time unit must be specified.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify units other than hours, minutes, seconds and
     * microseconds, since only the order of the four first parameters
     * is guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     */
    public function plus(
        int|Period $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($hours)) {
            $period = Period::of(
                0, 0, 0,
                $hours, $minutes, $seconds, $microseconds,
                0, 0, 0,
                0, 0,
                $milliseconds,
            );
        } elseif (
            !$minutes && !$seconds && !$microseconds
            && !$milliseconds
        ) {
            $period = $hours;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = $this->value->add($period->toNative());

        return new static($value);
    }

    /**
     * Returns a copy of this time with the specified amount of hours,
     * minutes, seconds and microseconds subtracted. It accepts a 
     * `Period` or individual time units.
     *
     * If a `Period` is passed as the first argument, no individual
     * time unit must be specified.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify units other than hours, minutes, seconds and
     * microseconds, since only the order of the four first parameters
     * is guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     */
    public function minus(
        int|Period $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($hours)) {
            $period = Period::of(
                0, 0, 0,
                $hours, $minutes, $seconds, $microseconds,
                0, 0, 0,
                0, 0,
                $milliseconds,
            );
        } elseif (
            !$minutes && !$seconds && !$microseconds
            && !$milliseconds
        ) {
            $period = $hours;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        $value = $this->value->sub($period->toNative());

        return new static($value);
    }

    /**
     * Returns a copy of this time with the specified hour, minute,
     * second and microsecond.
     *
     * @param int<0, 23>|null $hour
     * @param int<0, 59>|null $minute
     * @param int<0, 59>|null $second
     * @param int<0, 999999>|null $microsecond
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function with(
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static {
        if ($hour !== null) {
            Validator::hour($hour);
        } else {
            $hour = $this->hour();
        }

        if ($minute !== null) {
            Validator::minute($minute);
        } else {
            $minute = $this->minute();
        }

        if ($second !== null) {
            Validator::second($second);
        } else {
            $second = $this->second();
        }

        if ($microsecond !== null) {
            Validator::microsecond($microsecond);
        } else {
            $microsecond = $this->microsecond();
        }

        return new static($this->value->setTime($hour, $minute, $second, $microsecond));
    }

    /**
     * Makes a copy of this time with the specified amount of hours,
     * minutes, seconds and microseconds added. It works the same as
     * the {@see plus()} method, but returns a result instead of the
     * new time.
     *
     * The result will contain the new time if no error was found, or
     * an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>|Error<ArithmeticError>|Error<OutOfRangeException>
     */
    public function add(
        int|Period $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        int $milliseconds = 0,
    ): Ok|Error {
        try {
            $time = $this->plus(
                $hours, $minutes, $seconds, $microseconds,
                $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this time with the specified amount of hours,
     * minutes, seconds and microseconds subtracted. It works the same
     * as the {@see minus()} method, but returns a result instead of
     * the new time.
     *
     * The result will contain the new time if no error was found, or
     * an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>|Error<ArithmeticError>|Error<OutOfRangeException>
     */
    public function subtract(
        int|Period $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        int $milliseconds = 0,
    ): Ok|Error {
        try {
            $time = $this->minus(
                $hours, $minutes, $seconds, $microseconds,
                $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this time with the specified hour, minute,
     * second and microsecond. It works the same as the {@see with()}
     * method, but returns a result instead of the new time.
     *
     * The result will contain the new time if no error was found, or
     * an exception if something went wrong.
     *
     * @param int<0, 23>|null $hour
     * @param int<0, 59>|null $minute
     * @param int<0, 59>|null $second
     * @param int<0, 999999>|null $microsecond
     *
     * @return Ok<static>|Error<OutOfRangeException>
     */
    public function copy(
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): Ok|Error {
        try {
            $time = $this->with(
                $hour, $minute, $second, $microsecond,
            );
        } catch (OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }
}
