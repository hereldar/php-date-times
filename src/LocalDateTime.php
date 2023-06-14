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
use OutOfRangeException;
use Stringable;

/**
 * A date-time without a time-zone in the ISO-8601 calendar system,
 * such as 17:30:09 on 3 December 2007.
 *
 * This class does not store a time-zone. Instead, it is a description
 * of the date, as used for birthdays, combined with the local time as
 * seen on a wall clock. It cannot represent an instant on the
 * time-line without additional information such as an offset or
 * time-zone.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class LocalDateTime implements Datelike, Timelike, Formattable, Parsable, Stringable, Summable
{
    final public const ISO8601 = 'Y-m-d\TH:i:s';
    final public const ISO8601_MILLISECONDS = 'Y-m-d\TH:i:s.v';
    final public const ISO8601_MICROSECONDS = 'Y-m-d\TH:i:s.u';

    final public const RFC2822 = 'D, d M Y H:i:s';

    final public const RFC3339 = 'Y-m-d\TH:i:s';
    final public const RFC3339_MILLISECONDS = 'Y-m-d\TH:i:s.v';
    final public const RFC3339_MICROSECONDS = 'Y-m-d\TH:i:s.u';

    final public const SQL = 'Y-m-d H:i:s';
    final public const SQL_MILLISECONDS = 'Y-m-d H:i:s.v';
    final public const SQL_MICROSECONDS = 'Y-m-d H:i:s.u';

    /** @var array<class-string, static> */
    private static array $epochs = [];

    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    /**
     * Outputs this date-time as a `string`, using the default format
     * of the class.
     */
    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    /**
     * The Unix epoch (00:00:00 on 1 January 1970).
     */
    public static function epoch(): static
    {
        /** @psalm-suppress PropertyTypeCoercion */
        return self::$epochs[static::class] ??= static::of(1970, 1, 1, 0, 0, 0, 0);
    }

    /**
     * Obtains the current date-time from the system clock in the
     * specified time-zone. If no time-zone is specified, the `UTC`
     * time-zone will be used.
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
            return new static($dt);
        }

        $string = $dt->format('Y-n-j G:i:s.u');

        return static::parse($string, 'Y-n-j G:i:s.u')->orFail();
    }

    /**
     * Makes a new `LocalDateTime` with the specified year, month, day,
     * hour, minute, second and microsecond. The time units must be
     * within their valid range, otherwise an exception will be thrown.
     *
     * All parameters are optional and, if not specified, will take
     * their Unix epoch value (00:00:00 on 1 January 1970).
     *
     * @param int $year the year
     * @param int $month the month of the year, from 1 to 12
     * @param int $day the day of the month, from 1 to 31
     * @param int $hour the hour of the day, from 0 to 23
     * @param int $minute the minute of the hour, from 0 to 59
     * @param int $second the second of the minute, from 0 to 59
     * @param int $microsecond the microsecond of the second, from 0 to 999,999
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function of(
        int $year = 1970,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
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

        $dateTime = static::parse($string, 'Y-n-j G:i:s.u')->orFail();

        if ($extraYears !== 0) {
            return $dateTime->plus($extraYears);
        }

        return $dateTime;
    }

    /**
     * Makes a new `LocalDateTime` from a text string using a specific
     * format. It also accepts a list of formats.
     *
     * If the format is not specified, the ISO 8601 date-time format
     * will be used (`Y-m-d\TH:i:s`).
     *
     * The `LocalDateTime` is not returned directly, but a result that
     * will contain the date-time if no error was found, or an
     * exception if something went wrong.
     *
     * @param string|array<int, string> $format
     *
     * @throws InvalidArgumentException if an empty list of formats is passed
     *
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string|array $format = LocalDateTime::ISO8601,
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
     * Makes a new `LocalDateTime` from a text with the ISO 8601
     * date-time format (e.g. `'2023-02-17T17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17T17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
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
     * Makes a new `LocalDateTime` from a text with the RFC 2822
     * date-time format (e.g. `'Fri, 17 Feb 2023 17:30:09'`).
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, self::RFC2822)->orFail();
    }

    /**
     * Makes a new `LocalDateTime` from a text with the RFC 3339
     * date-time format (e.g. `'2023-02-17T17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17T17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
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
     * Makes a new `LocalDateTime` from a text with the SQL date-time
     * format (e.g. `'2023-02-17 17:30:09'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17 17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17 17:30:09.382172'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
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
     * Makes a new `LocalDateTime` from a native `DateTime` or
     * `DateTimeImmutable`.
     *
     * Only the date and time values will be taken, while time-zone
     * values will be ignored.
     */
    public static function fromNative(NativeDateTimeInterface $value): static
    {
        $string = $value->format('Y-n-j G:i:s.u');

        return static::parse($string, 'Y-n-j G:i:s.u')->orFail();
    }

    /**
     * Formats this date-time using the specified format.
     *
     * If the format is not specified, the ISO 8601 date-time format
     * will be used (`Y-m-d\TH:i:s`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = LocalDateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    /**
     * Formats this date-time with the ISO 8601 date-time format (e.g.
     * `'2023-02-17T17:30:09'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17T17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172'`) by setting respectively
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
     * Formats this date-time with the RFC 2822 date-time format (e.g.
     * `'Fri, 17 Feb 2023 17:30:09'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc2822(): string
    {
        return $this->value->format(self::RFC2822);
    }

    /**
     * Formats this date-time with the RFC 3339 date-time format (e.g.
     * `'2023-02-17T17:30:09'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17T17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172'`) by setting respectively
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
     * Formats this date-time with the SQL date-time format (e.g.
     * `'2023-02-17 17:30:09'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17 17:30:09.105'`) or microseconds (e.g.
     * `'2023-02-17 17:30:09.382172'`) by setting respectively
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
     * date-time.
     *
     * The time-zone value will be taken from the Unix epoch (UTC).
     */
    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    /**
     * Combines this date-time with a time-zone to make a `DateTime`.
     * It accepts a `TimeZone` object or a text with its name.
     */
    public function atTimeZone(TimeZone|string $timeZone): DateTime
    {
        return DateTime::parse(
            $this->value->format('Y-n-j G:i:s.u'),
            'Y-n-j G:i:s.u',
            $timeZone
        )->orFail();
    }

    /**
     * Combines this date-time with an offset to make a `DateTime`. It
     * accepts an `Offset` or individual time units.
     *
     * If an `Offset` is passed as the first argument, no further
     * arguments will be accepted.
     *
     * If individual time units are passed, they must be within their
     * valid range. Missing units will be zero (00:00:00).
     *
     * @throws InvalidArgumentException if an `Offset` is combined with some time units
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function atOffset(
        Offset|int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): DateTime {
        if (is_int($hours)) {
            $offset = Offset::of($hours, $minutes, $seconds);
        } elseif (!$minutes && !$seconds) {
            $offset = $hours;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when an offset is passed'
            );
        }

        return DateTime::parse(
            $this->value->format('Y-n-j G:i:s.u'),
            'Y-n-j G:i:s.u',
            $offset
        )->orFail();
    }

    /**
     * Returns a `LocalDate` with the same year, month and day as this
     * date-time.
     */
    public function date(): LocalDate
    {
        return LocalDate::fromNative($this->value);
    }

    /**
     * Returns the year.
     */
    public function year(): int
    {
        return (int) $this->value->format('Y');
    }

    /**
     * Returns the month as an `int` from 1 to 12.
     *
     * @return int<1, 12>
     */
    public function month(): int
    {
        /** @var int<1, 12> */
        return (int) $this->value->format('n');
    }

    /**
     * Returns the ISO 8601 week number of year (weeks starting on
     * Monday).
     */
    public function week(): int
    {
        return (int) $this->value->format('W');
    }

    /**
     * Returns the ISO 8601 week-numbering year. This has the same
     * value as the normal year, except that if the ISO week number
     * belongs to the previous or next year, that year is used
     * instead.
     */
    public function weekYear(): int
    {
        return (int) $this->value->format('o');
    }

    /**
     * Returns the day of the month.
     *
     * @return int<1, 31>
     */
    public function day(): int
    {
        /** @var int<1, 31> */
        return (int) $this->value->format('j');
    }

    /**
     * Returns the day of the week as an `int` from 1 to 7.
     *
     * @return int<1, 7>
     */
    public function dayOfWeek(): int
    {
        /** @var int<1, 7> */
        return (int) $this->value->format('N');
    }

    /**
     * Returns the day of the year as an `int` from 1 to 366.
     *
     * @return int<1, 366>
     */
    public function dayOfYear(): int
    {
        /** @var int<1, 366> */
        return (int) $this->value->format('z') + 1;
    }

    /**
     * Returns whether it is a leap year.
     */
    public function inLeapYear(): bool
    {
        return ($this->value->format('L') === '1');
    }

    /**
     * Returns a `LocalTime` with the same hour, minute, second and
     * microsecond as this date-time.
     */
    public function time(): LocalTime
    {
        return LocalTime::fromNative($this->value);
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
     * Compares this date-time to another date-time.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * date-time is before, equal to, or after the given date-time.
     */
    public function compareTo(LocalDateTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    /**
     * Checks if the given date-time belongs to the same class and has
     * the same value as this date-time.
     */
    public function is(LocalDateTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    /**
     * Checks if the given date-time belongs to another class or has
     * a different value than this date-time.
     */
    public function isNot(LocalDateTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    /**
     * Checks if the given date-time has the same value as this
     * date-time.
     */
    public function isEqual(LocalDateTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    /**
     * Checks if the given date-time has a different value from this
     * date-time.
     */
    public function isNotEqual(LocalDateTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    /**
     * Checks if this date-time is after the specified date-time.
     */
    public function isGreater(LocalDateTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    /**
     * Checks if this date-time is after or equal to the specified
     * date-time.
     */
    public function isGreaterOrEqual(LocalDateTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    /**
     * Checks if this date-time is before the specified date-time.
     */
    public function isLess(LocalDateTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    /**
     * Checks if this date-time is before or equal to the specified
     * date-time.
     */
    public function isLessOrEqual(LocalDateTime $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    /**
     * Returns a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * added. It accepts a `Period` or individual time units.
     *
     * If a `Period` is passed as the first argument, no individual
     * time unit must be specified.
     *
     * In some cases, adding the amount may make the resulting date
     * invalid. For example, adding a month to 31 January would result
     * in 31 February. In cases like this, the previous valid date
     * will be returned, which would be the last valid day of February
     * in this example.
     *
     * This behaviour can be changed by setting `$overflow` to true.
     * If so, the overflow amount will be added to the following month,
     * which would result in 3 March or 2 March in this example.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify overflow and units other than years, months, days,
     * hours, minutes, seconds and microseconds, since only the order
     * of the seven first parameters is guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     */
    public function plus(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                $milliseconds,
            );
        } elseif (
            !$months && !$days
            && !$hours && !$minutes && !$seconds && !$microseconds
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks
            && !$milliseconds
        ) {
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

    /**
     * Returns a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * subtracted. It accepts a `Period` or individual time units.
     *
     * If a `Period` is passed as the first argument, no individual
     * time unit must be specified.
     *
     * In some cases, subtracting the amount may make the resulting
     * date invalid. For example, subtracting a year from 29 February
     * 2008 would result in 29 February 2007 (standard year). In cases
     * like this, the last valid day of the month will be returned,
     * which would be 28 February 2007 in this example.
     *
     * This behaviour can be changed by setting `$overflow` to true.
     * If so, the overflow amount will be added to the following month,
     * which would result in 1 March in this example.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify overflow and units other than years, months, days,
     * hours, minutes, seconds and microseconds, since only the order
     * of the seven first parameters is guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     */
    public function minus(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                $milliseconds,
            );
        } elseif (
            !$months && !$days
            && !$hours && !$minutes && !$seconds && !$microseconds
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks
            && !$milliseconds
        ) {
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

    /**
     * Returns a copy of this date-time with the specified year, month,
     * day, hour, minute, second and microsecond.
     *
     * @param ?int $year the year
     * @param ?int $month the month of the year, from 1 to 12
     * @param ?int $day the day of the month, from 1 to 31
     * @param ?int $hour the hour of the day, from 0 to 23
     * @param ?int $minute the minute of the hour, from 0 to 59
     * @param ?int $second the second of the minute, from 0 to 59
     * @param ?int $microsecond the microsecond of the second, from 0 to 999,999
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
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
            if ($year === null) {
                $year = $this->year();
            }

            if ($month !== null) {
                Validator::month($month);
            } else {
                $month = $this->month();
            }

            if ($day !== null) {
                Validator::day($day, $month, $year);
            } else {
                $day = $this->day();
            }

            $dt = $dt->setDate($year, $month, $day);
        }

        if ($hour !== null
            || $minute !== null
            || $second !== null
            || $microsecond !== null) {
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

            $dt = $dt->setTime($hour, $minute, $second, $microsecond);
        }

        return new static($dt);
    }

    /**
     * Makes a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * added. It works the same as the {@see plus()} method, but
     * returns a result instead of the new date-time.
     *
     * The result will contain the new date-time if no error was found,
     * or an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>
     */
    public function add(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): Ok {
        /** @var Ok<static> */
        return Ok::withValue($this->plus(
            $years, $months, $days,
            $hours, $minutes, $seconds, $microseconds,
            $overflow,
            $millennia, $centuries, $decades,
            $quarters, $weeks,
            $milliseconds,
        ));
    }

    /**
     * Makes a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * subtracted. It works the same as the {@see minus()} method, but
     * returns a result instead of the new date-time.
     *
     * The result will contain the new date-time if no error was found,
     * or an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>
     */
    public function subtract(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): Ok {
        /** @var Ok<static> */
        return Ok::withValue($this->minus(
            $years, $months, $days,
            $hours, $minutes, $seconds, $microseconds,
            $overflow,
            $millennia, $centuries, $decades,
            $quarters, $weeks,
            $milliseconds,
        ));
    }

    /**
     * Makes a copy of this date with the specified year, month, day,
     * hour, minute, second and microsecond. It works the same as the
     * {@see with()} method, but returns a result instead of the new
     * date-time.
     *
     * The result will contain the new date-time if no error was found,
     * or an exception if something went wrong.
     *
     * @param ?int $year the year
     * @param ?int $month the month of the year, from 1 to 12
     * @param ?int $day the day of the month, from 1 to 31
     * @param ?int $hour the hour of the day, from 0 to 23
     * @param ?int $minute the minute of the hour, from 0 to 59
     * @param ?int $second the second of the minute, from 0 to 59
     * @param ?int $microsecond the microsecond of the second, from 0 to 999,999
     *
     * @return Ok<static>|Error<OutOfRangeException>
     */
    public function copy(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): Ok|Error {
        try {
            $dateTime = $this->with(
                $year, $month, $day,
                $hour, $minute, $second, $microsecond,
            );
        } catch (OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
