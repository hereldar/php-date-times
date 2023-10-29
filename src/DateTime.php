<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTime as MutableNativeDateTime;
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
use Hereldar\DateTimes\Interfaces\Timelike;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use OutOfRangeException;
use Stringable;

/**
 * A date and time with a time-zone in the ISO-8601 calendar system,
 * such as 17:30:09 America/Mexico_City on 3 December 2007.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class DateTime implements Datelike, Timelike, Formattable, Stringable, Copyable, Summable
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
    private static array $epochs = [];

    /**
     * @internal
     */
    private function __construct(
        private readonly NativeDateTime $value
    ) {}

    /**
     * Outputs this date-time as a `string`, using the default format
     * of the class.
     */
    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    /**
     * The Unix epoch (00:00:00 UTC on 1 January 1970).
     */
    public static function epoch(): static
    {
        /** @psalm-suppress PropertyTypeCoercion */
        return self::$epochs[static::class] ??= static::of(1970, 1, 1, 0, 0, 0, 0, 'UTC');
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

        return new static(new NativeDateTime('now', $tz));
    }

    /**
     * Makes a new `DateTime` with the specified year, month, day,
     * hour, minute, second, microsecond and time-zone. The time units
     * must be within their valid range, otherwise an exception will
     * be thrown.
     * All parameters are optional and, if not specified, will take
     * their Unix epoch value (00:00:00 UTC on 1 January 1970).
     *
     * @param int $year the year
     * @param int $month the month of the year, from 1 to 12
     * @param int $day the day of the month, from 1 to 31
     * @param int $hour the hour of the day, from 0 to 23
     * @param int $minute the minute of the hour, from 0 to 59
     * @param int $second the second of the minute, from 0 to 59
     * @param int $microsecond the microsecond of the second, from 0 to 999,999
     * @param TimeZone|Offset|string $timeZone the time-zone name or the offset from UTC/Greenwich
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     * @throws TimeZoneException if the time-zone name cannot be found
     */
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

    /**
     * Makes a new `DateTime` from a text string using a specific
     * format. It also accepts a list of formats.
     * If the format is not specified, the ISO 8601 date-time format
     * will be used (`Y-m-d\TH:i:sp`).
     * The `DateTime` is not returned directly, but a result that will
     * contain the date-time if no error was found, or an exception if
     * something went wrong.
     *
     * @param string|array<int, string> $format
     *
     * @throws TimeZoneException if the time-zone name cannot be found
     * @throws InvalidArgumentException if an empty list of formats is passed
     *
     * @return Ok<static>|Error<ParseException>
     */
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

    /**
     * Makes a new `DateTime` from a text with one of the cookie
     * date-time formats (e.g. `'Fri, 17 Feb 2023 17:30:09 UTC'`).
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromCookie(string $value): static
    {
        return static::parse($value, self::COOKIE_VARIANTS)->orFail();
    }

    /**
     * Makes a new `DateTime` from a text with one of the HTTP
     * date-time formats (e.g. `'Fri, 17 Feb 2023 17:30:09 GMT'`).
     *
     * The date-time is returned directly if no error is found,
     * otherwise an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     */
    public static function fromHttp(string $value): static
    {
        return static::parse($value, self::HTTP_VARIANTS)->orFail();
    }

    /**
     * Makes a new `DateTime` from a text with the ISO 8601 date-time
     * format (e.g. `'2023-02-17T17:30:09Z'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17T17:30:09.105Z'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172Z'`) by setting respectively
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
     * Makes a new `DateTime` from a text with the RFC 2822 date-time
     * format (e.g. `'Fri, 17 Feb 2023 17:30:09 +0000'`).
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
     * Makes a new `DateTime` from a text with the RFC 3339 date-time
     * format (e.g. `'2023-02-17T17:30:09+00:00'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17T17:30:09.105+00:00'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172+00:00'`) by setting respectively
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
     * Makes a new `DateTime` from a text with the SQL date-time
     * format (e.g. `'2023-02-17 17:30:09+00:00'`).
     *
     * It is possible to parse texts with milliseconds (e.g.
     * `'2023-02-17 17:30:09.105+00:00'`) or microseconds (e.g.
     * `'2023-02-17 17:30:09.382172+00:00'`) by setting respectively
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
     * Makes a new `DateTime` from a native `DateTime` or
     * `DateTimeImmutable`.
     */
    public static function fromNative(NativeDateTimeInterface $value): static
    {
        if ($value instanceof MutableNativeDateTime) {
            $value = NativeDateTime::createFromMutable($value);
        }

        return new static($value);
    }

    /**
     * Makes a new `DateTime` from a given number of seconds after the
     * Unix epoch (00:00:00 UTC on 1 January 1970).
     */
    public static function fromSecondsSinceEpoch(int $seconds): static
    {
        $timestamp = (string) $seconds;
        $tz = TimeZone::utc()->toNative();

        return self::parseSimple($timestamp, 'U', $tz)->orFail();
    }

    /**
     * Makes a new `DateTime` from a given number of seconds and
     * microseconds after the Unix epoch (00:00:00 UTC on 1 January
     * 1970).
     */
    public static function fromMicrosecondsSinceEpoch(int $seconds, int $microseconds): static
    {
        $timestamp = sprintf('%d.%06d', $seconds, $microseconds);
        $tz = TimeZone::utc()->toNative();

        return self::parseSimple($timestamp, 'U.u', $tz)->orFail();
    }

    /**
     * Formats this date-time using the specified format.
     *
     * If the format is not specified, the ISO 8601 date-time format
     * will be used (`Y-m-d\TH:i:sp`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = DateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->formatted($format));
    }

    /**
     * Formats this date-time using the specified format.
     *
     * If the format is not specified, the ISO 8601 date-time format
     * will be used (`Y-m-d\TH:i:sp`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws FormatException
     */
    public function formatted(string $format = DateTime::ISO8601): string
    {
        return $this->value->format($format);
    }

    /**
     * Formats this date-time with the main cookie date-time format
     * (e.g. `'Fri, 17 Feb 2023 17:30:09 UTC'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toCookie(): string
    {
        return $this->formatted(self::COOKIE);
    }

    /**
     * Formats this date-time with the main HTTP date-time format (e.g.
     * `'Fri, 17 Feb 2023 17:30:09 GMT'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toHttp(): string
    {
        $tz = TimeZone::utc()->toNative();

        return $this->value->setTimezone($tz)->format(self::HTTP);
    }

    /**
     * Formats this date-time with the ISO 8601 date-time format (e.g.
     * `'2023-02-17T17:30:09Z'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17T17:30:09.105Z'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172Z'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toIso8601(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->formatted(match (true) {
            $microseconds => self::ISO8601_MICROSECONDS,
            $milliseconds => self::ISO8601_MILLISECONDS,
            default => self::ISO8601,
        });
    }

    /**
     * Formats this date-time with the RFC 2822 date-time format (e.g.
     * `'Fri, 17 Feb 2023 17:30:09 +0000'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc2822(): string
    {
        return $this->formatted(self::RFC2822);
    }

    /**
     * Formats this date-time with the RFC 3339 date-time format (e.g.
     * `'2023-02-17T17:30:09+00:00'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17T17:30:09.105+00:00'`) or microseconds (e.g.
     * `'2023-02-17T17:30:09.382172+00:00'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc3339(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->formatted(match (true) {
            $microseconds => self::RFC3339_MICROSECONDS,
            $milliseconds => self::RFC3339_MILLISECONDS,
            default => self::RFC3339,
        });
    }

    /**
     * Formats this date-time with the SQL date-time format (e.g.
     * `'2023-02-17 17:30:09+00:00'`).
     *
     * It is possible to add milliseconds (e.g.
     * `'2023-02-17 17:30:09.105+00:00'`) or microseconds (e.g.
     * `'2023-02-17 17:30:09.382172+00:00'`) by setting respectively
     * `$milliseconds` or `$microseconds` to true.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toSql(
        bool $milliseconds = false,
        bool $microseconds = false,
    ): string {
        return $this->formatted(match (true) {
            $microseconds => self::SQL_MICROSECONDS,
            $milliseconds => self::SQL_MILLISECONDS,
            default => self::SQL,
        });
    }

    /**
     * Returns a native `DateTimeImmutable` with the values of this
     * date-time.
     */
    public function toNative(): NativeDateTime
    {
        return $this->value;
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
     * Returns the offset of the local date-time from UTC/Greenwich.
     */
    public function offset(): Offset
    {
        return Offset::fromTotalSeconds(
            $this->value->getOffset()
        );
    }

    /**
     * Returns the time-zone, such as `America/Mexico_City`.
     */
    public function timeZone(): TimeZone
    {
        return TimeZone::fromNative(
            $this->value->getTimezone()
        );
    }

    /**
     * Returns whether in daylight saving time.
     */
    public function inDaylightSavingTime(): bool
    {
        return ($this->value->format('I') === '1');
    }

    /**
     * Returns the number of seconds after the  Unix epoch (00:00:00
     * UTC on 1 January 1970).
     */
    public function secondsSinceEpoch(): int
    {
        return $this->value->getTimestamp();
    }

    /**
     * Returns the number of seconds and microseconds after the  Unix
     * epoch (00:00:00 UTC on 1 January 1970).
     *
     * @return array{0: int, 1: int}
     */
    public function microsecondsSinceEpoch(): array
    {
        return [$this->value->getTimestamp(), $this->microsecond()];
    }

    /**
     * Compares this date-time to another date-time.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * date-time is before, equal to, or after the given date-time.
     */
    public function compareTo(DateTime $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    /**
     * Checks if the given date-time belongs to the same class and has
     * the same value as this date-time.
     */
    public function is(DateTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    /**
     * Checks if the given date-time belongs to another class or has a
     * different value than this date-time.
     */
    public function isNot(DateTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    /**
     * Checks if the given date-time has the same value as this
     * date-time.
     */
    public function isEqual(DateTime $that): bool
    {
        return ($this->value == $that->toNative());
    }

    /**
     * Checks if the given date-time has a different value from this
     * date-time.
     */
    public function isNotEqual(DateTime $that): bool
    {
        return ($this->value != $that->toNative());
    }

    /**
     * Checks if this date-time is after the specified date-time.
     */
    public function isGreater(DateTime $that): bool
    {
        return ($this->value > $that->toNative());
    }

    /**
     * Checks if this date-time is after or equal to the specified
     * date-time.
     */
    public function isGreaterOrEqual(DateTime $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    /**
     * Checks if this date-time is before the specified date-time.
     */
    public function isLess(DateTime $that): bool
    {
        return ($this->value < $that->toNative());
    }

    /**
     * Checks if this date-time is before or equal to the specified
     * date-time.
     */
    public function isLessOrEqual(DateTime $that): bool
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
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
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
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
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
     * day, hour, minute, second, microsecond and time-zone.
     *
     * @param ?int $year the year
     * @param ?int $month the month of the year, from 1 to 12
     * @param ?int $day the day of the month, from 1 to 31
     * @param ?int $hour the hour of the day, from 0 to 23
     * @param ?int $minute the minute of the hour, from 0 to 59
     * @param ?int $second the second of the minute, from 0 to 59
     * @param ?int $microsecond the microsecond of the second, from 0 to 999,999
     * @param TimeZone|Offset|string|null $timeZone the time-zone name or the offset from UTC/Greenwich
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     * @throws TimeZoneException if the time-zone name cannot be found
     */
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

        if ($timeZone !== null) {
            $tz = match (true) {
                is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
                $timeZone instanceof TimeZone => $timeZone->toNative(),
                $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
            };
            /** @var NativeDateTime $dt */
            $dt = NativeDateTime::createFromFormat(
                'Y-n-j G:i:s.u',
                $dt->format('Y-n-j G:i:s.u'),
                $tz,
            );
        }

        return new static($dt);
    }

    /**
     * Makes a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * added.
     *
     * It works the same as the {@see plus()} method, but returns a
     * result instead of the new date-time.
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
     * @return Ok<static>|Error<ArithmeticError>
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
    ): Ok|Error {
        try {
            $time = $this->plus(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $overflow,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this date-time with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * subtracted.
     *
     * It works the same as the {@see minus()} method, but returns a
     * result instead of the new date-time.
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
     * @return Ok<static>|Error<ArithmeticError>
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
    ): Ok|Error {
        try {
            $time = $this->minus(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $overflow,
                $millennia, $centuries, $decades,
                $quarters, $weeks,
                $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }

    /**
     * Makes a copy of this date-time with the specified year, month,
     * day, hour, minute, second, microsecond and time-zone.
     *
     * It works the same as the {@see with()} method, but returns a
     * result instead of the new date-time.
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
     * @param TimeZone|Offset|string|null $timeZone the time-zone name or the offset from UTC/Greenwich
     *
     * @return Ok<static>|Error<OutOfRangeException|TimeZoneException>
     */
    public function copy(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
        TimeZone|Offset|string|null $timeZone = null,
    ): Ok|Error {
        try {
            $dateTime = $this->with(
                $year, $month, $day,
                $hour, $minute, $second, $microsecond,
                $timeZone,
            );
        } catch (OutOfRangeException|TimeZoneException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
