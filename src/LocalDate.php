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
use Hereldar\DateTimes\Interfaces\Copyable;
use Hereldar\DateTimes\Interfaces\Datelike;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use OutOfRangeException;
use Stringable;

/**
 * A date without a time-zone in the ISO-8601 calendar system, such as
 * 3 December 2007.
 *
 * This class does not store a time or time-zone. Instead, it is a
 * description of the date, as used for birthdays. It cannot represent
 * an instant on the time-line without additional information such as
 * an offset or time-zone.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class LocalDate implements Datelike, Formattable, Stringable, Copyable, Summable
{
    final public const ISO8601 = 'Y-m-d';
    final public const RFC2822 = 'D, d M Y';
    final public const RFC3339 = 'Y-m-d';
    final public const SQL = 'Y-m-d';

    /** @var array<class-string, static> */
    private static array $epochs = [];

    /**
     * @internal
     */
    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    /**
     * Outputs this date as a `string`, using the default format of
     * the class.
     */
    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    /**
     * The Unix epoch (1 January 1970).
     */
    public static function epoch(): static
    {
        /** @psalm-suppress PropertyTypeCoercion */
        return self::$epochs[static::class] ??= static::of(1970, 1, 1);
    }

    /**
     * Obtains the current date from the system clock in the specified
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

        $dt = new NativeDateTime('today', $tz);

        if ($timeZone === 'UTC' || $tz->getName() === 'UTC') {
            return new static($dt);
        }

        $string = $dt->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    /**
     * Makes a new `LocalDate` with the specified year, month and
     * day-of-month. The day must be valid for the given year and
     * month, otherwise an exception will be thrown.
     *
     * All parameters are optional and, if not specified, will take
     * their Unix epoch value (1 January 1970).
     *
     * @param int $year the year
     * @param int $month the month of the year, from 1 to 12
     * @param int $day the day of the month, from 1 to 31
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function of(
        int $year = 1970,
        int $month = 1,
        int $day = 1,
    ): static {
        Validator::month($month);
        Validator::day($day, $month, $year);

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
            '%04d-%d-%d',
            $year,
            $month,
            $day,
        );

        $date = static::parse($string, 'Y-n-j')->orFail();

        if ($extraYears !== 0) {
            return $date->plus($extraYears);
        }

        return $date;
    }

    /**
     * Makes a new `LocalDate` from a year and day-of-year. The day
     * must be valid for the given year, otherwise an exception will
     * be thrown.
     *
     * Both parameters are optional and, if not specified, will take
     * their Unix epoch value (1st of 1970).
     *
     * @param int $year the year
     * @param int $day the day of the year, from 1 to 366
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function fromDayOfYear(
        int $year = 1970,
        int $day = 1,
    ): static {
        Validator::dayOfYear($day, $year);

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
            '%04d-%d',
            $year,
            $day - 1,
        );

        $dateTime = static::parse($string, 'Y-z')->orFail();

        if ($extraYears !== 0) {
            return $dateTime->plus($extraYears);
        }

        return $dateTime;
    }

    /**
     * Makes a new `LocalDate` from a text string using a specific
     * format. It also accepts a list of formats.
     *
     * If the format is not specified, the ISO 8601 date format will
     * be used (`Y-m-d`).
     *
     * The `LocalDate` is not returned directly, but a result that
     * will contain the date if no error was found, or an exception if
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
        string|array $format = LocalDate::ISO8601,
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
     * Makes a new `LocalDate` from a text with the ISO 8601 date
     * format (e.g. `'2023-02-17'`).
     *
     * The date is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromIso8601(string $value): static
    {
        return static::parse($value, self::ISO8601)->orFail();
    }

    /**
     * Makes a new `LocalDate` from a text with the RFC 2822 date
     * format (e.g. `'Fri, 17 Feb 2023'`).
     *
     * The date is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, self::RFC2822)->orFail();
    }

    /**
     * Makes a new `LocalDate` from a text with the RFC 3339 date
     * format (e.g. `'2023-02-17'`).
     *
     * The date is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromRfc3339(string $value): static
    {
        return static::parse($value, self::RFC3339)->orFail();
    }

    /**
     * Makes a new `LocalDate` from a text with the SQL date format
     * (e.g. `'2023-02-17'`).
     *
     * The date is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromSql(string $value): static
    {
        return static::parse($value, self::SQL)->orFail();
    }

    /**
     * Makes a new `LocalDate` from a native `DateTime` or
     * `DateTimeImmutable`.
     *
     * Only the date values will be taken, while time and time-zone
     * values will be ignored.
     */
    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    /**
     * Formats this date using the specified format.
     *
     * If the format is not specified, the ISO 8601 date format will
     * be used (`Y-m-d`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = LocalDate::ISO8601): Ok|Error
    {
        return Ok::withValue($this->formatted($format));
    }

    /**
     * Formats this date using the specified format.
     *
     * If the format is not specified, the ISO 8601 date format will
     * be used (`Y-m-d`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws FormatException
     */
    public function formatted(string $format = LocalDate::ISO8601): string
    {
        return $this->value->format($format);
    }

    /**
     * Formats this date with the ISO 8601 date format (e.g.
     * `'2023-02-17'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toIso8601(): string
    {
        return $this->formatted(self::ISO8601);
    }

    /**
     * Formats this date with the RFC 2822 date format (e.g.
     * `'Fri, 17 Feb 2023'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc2822(): string
    {
        return $this->formatted(self::RFC2822);
    }

    /**
     * Formats this date with the RFC 3339 date format (e.g.
     * `'2023-02-17'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc3339(): string
    {
        return $this->formatted(self::RFC3339);
    }

    /**
     * Formats this date with the SQL date format (e.g.
     * `'2023-02-17'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toSql(): string
    {
        return $this->formatted(self::SQL);
    }

    /**
     * Returns a native `DateTimeImmutable` with the values of this
     * date.
     *
     * The time and time-zone values will be taken from the Unix epoch
     * (00:00:00 UTC).
     */
    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    /**
     * Combines this date with a time to make a `LocalDateTime`. It
     * accepts a `LocalTime` or individual time units.
     *
     * If a `LocalTime` is passed as the first argument, no further
     * arguments will be accepted.
     *
     * If individual time units are passed, they must be within their
     * valid range. Missing units will be taken from the Unix epoch
     * (00:00:00).
     *
     * @throws InvalidArgumentException if a `LocalTime` is combined with some time units
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function atTime(
        LocalTime|int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): LocalDateTime {
        if (is_int($hour)) {
            Validator::hour($hour);
            Validator::minute($minute);
            Validator::second($second);
            Validator::microsecond($microsecond);
        } elseif (!$minute && !$second && !$microsecond) {
            $time = $hour;
            $hour = $time->hour();
            $minute = $time->minute();
            $second = $time->second();
            $microsecond = $time->microsecond();
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a local time is passed'
            );
        }

        $dt = $this->value->setTime(
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        return LocalDateTime::fromNative($dt);
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
     * Compares this date to another date.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * date is before, equal to, or after the given date.
     */
    public function compareTo(LocalDate $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    /**
     * Checks if the given date belongs to the same class and has the
     * same value as this date.
     */
    public function is(LocalDate $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    /**
     * Checks if the given date belongs to another class or has a
     * different value than this date.
     */
    public function isNot(LocalDate $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    /**
     * Checks if the given date has the same value as this date.
     */
    public function isEqual(LocalDate $that): bool
    {
        return ($this->value == $that->toNative());
    }

    /**
     * Checks if the given date has a different value from this date.
     */
    public function isNotEqual(LocalDate $that): bool
    {
        return ($this->value != $that->toNative());
    }

    /**
     * Checks if this date is after the specified date.
     */
    public function isGreater(LocalDate $that): bool
    {
        return ($this->value > $that->toNative());
    }

    /**
     * Checks if this date is after or equal to the specified date.
     */
    public function isGreaterOrEqual(LocalDate $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    /**
     * Checks if this date is before the specified date.
     */
    public function isLess(LocalDate $that): bool
    {
        return ($this->value < $that->toNative());
    }

    /**
     * Checks if this date is before or equal to the specified date.
     */
    public function isLessOrEqual(LocalDate $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    /**
     * Returns a copy of this date with the specified amount of years,
     * months and days added. It accepts a `Period` or individual time
     * units.
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
     * specify overflow and units other than years, months and days,
     * since only the order of the three first parameters is
     * guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public function plus(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $days,
                0, 0, 0, 0,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                0,
            );
        } elseif (
            !$months && !$days
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks
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
     * Returns a copy of this date with the specified amount of years,
     * months and days subtracted. It accepts a `Period` or individual
     * time units.
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
     * specify overflow and units other than years, months and days,
     * since only the order of the three first parameters is
     * guaranteed.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public function minus(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
                $years, $months, $days,
                0, 0, 0, 0,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                0,
            );
        } elseif (
            !$months && !$days
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks
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
     * Returns a copy of this date with the specified year, month and
     * day.
     *
     * @param ?int $year the year
     * @param ?int $month the month of the year, from 1 to 12
     * @param ?int $day the day of the month, from 1 to 31
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
    ): static {
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

        return new static($this->value->setDate($year, $month, $day));
    }

    /**
     * Makes a copy of this date with the specified amount of years,
     * months and days added.
     *
     * It works the same as the {@see plus()} method, but returns a
     * result instead of the new date.
     *
     * The result will contain the new date if no error was found, or
     * an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function add(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
    ): Ok|Error {
        try {
            $time = $this->plus(
                $years, $months, $days,
                $overflow,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this date with the specified amount of years,
     * months and days subtracted.
     *
     * It works the same as the {@see minus()} method, but returns a
     * result instead of the new date.
     *
     * The result will contain the new date if no error was found, or
     * an exception if something went wrong.
     *
     * However, if a `Period` is combined with any time unit, the
     * exception will not be captured, allowing it to be thrown
     * normally.
     *
     * @throws InvalidArgumentException if a `Period` is combined with some time units
     *
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function subtract(
        int|Period $years = 0,
        int $months = 0,
        int $days = 0,
        bool $overflow = false,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
    ): Ok|Error {
        try {
            $time = $this->minus(
                $years, $months, $days,
                $overflow,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this date with the specified year, month and
     * day.
     *
     * It works the same as the {@see with()} method, but returns a
     * result instead of the new date.
     *
     * The result will contain the new date if no error was found, or
     * an exception if something went wrong.
     *
     * @param ?int $year the year
     * @param ?int $month the month of the year, from 1 to 12
     * @param ?int $day the day of the month, from 1 to 31
     *
     * @return Ok<static>|Error<OutOfRangeException>
     */
    public function copy(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
    ): Ok|Error {
        try {
            $date = $this->with(
                $year, $month, $day,
            );
        } catch (OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($date);
    }
}
