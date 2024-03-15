<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\Copyable;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use OutOfRangeException;
use Stringable;

/**
 * A time offset from Greenwich/UTC, such as `-06:00`.
 *
 * Although time offsets usually consist of a fixed number of hours
 * and minutes, this class also supports seconds.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class Offset implements Formattable, Stringable, Copyable, Summable
{
    final public const ISO8601 = '%R%H:%I';
    final public const ISO8601_SECONDS = '%R%H:%I:%S';
    final public const RFC2822 = '%R%H%I';
    final public const RFC3339 = '%R%H:%I';
    final public const RFC3339_SECONDS = '%R%H:%I:%S';
    final public const SQL = '%R%H:%I';
    final public const SQL_SECONDS = '%R%H:%I:%S';

    public const HOURS_MAX = +self::HOURS_LIMIT;
    public const HOURS_MIN = -self::HOURS_LIMIT;
    public const MINUTES_MAX = +self::MINUTES_LIMIT;
    public const MINUTES_MIN = -self::MINUTES_LIMIT;
    public const SECONDS_MAX = +self::SECONDS_LIMIT;
    public const SECONDS_MIN = -self::SECONDS_LIMIT;
    public const TOTAL_MINUTES_MAX = +self::TOTAL_MINUTES_LIMIT;
    public const TOTAL_MINUTES_MIN = -self::TOTAL_MINUTES_LIMIT;
    public const TOTAL_SECONDS_MAX = +self::TOTAL_SECONDS_LIMIT;
    public const TOTAL_SECONDS_MIN = -self::TOTAL_SECONDS_LIMIT;

    private const HOURS_LIMIT = 15;
    private const MINUTES_LIMIT = 59;
    private const SECONDS_LIMIT = 59;
    private const TOTAL_MINUTES_LIMIT = self::HOURS_LIMIT * 60;
    private const TOTAL_SECONDS_LIMIT = self::HOURS_LIMIT * 3600;

    /** @var array<class-string, array<int, static>> */
    private static array $offsets = [];

    private const ISO8601_PATTERN = <<<'REGEX'
        /
            (?P<sign>[+-])
            (?P<hours>[0-9]{2})
            \:
            (?P<minutes>[0-9]{2})
            (?:
                \:
                (?P<seconds>[0-9]{2})
            )?
        /xS
        REGEX;

    private const RFC2822_PATTERN = <<<'REGEX'
        /
            (?P<sign>[+-])
            (?P<hours>[0-9]{2})
            (?P<minutes>[0-9]{2})
            (?:
                (?P<seconds>[0-9]{2})
            )?
        /xS
        REGEX;

    private const FORMAT_PATTERN = '/%([%a-zA-Z])/';

    /**
     * @internal
     */
    private function __construct(
        private readonly int $value,
    ) {}

    /**
     * Outputs this offset as a `string`, using the ISO 8601 format.
     */
    public function __toString(): string
    {
        return $this->toIso8601();
    }

    /**
     * The offset for UTC (00:00:00).
     */
    public static function zero(): static
    {
        return static::fromTotalSeconds(0);
    }

    /**
     * The maximum supported offset (15:00:00).
     */
    public static function max(): static
    {
        return static::fromTotalSeconds(self::TOTAL_SECONDS_MAX);
    }

    /**
     * The minimum supported offset (-15:00:00).
     */
    public static function min(): static
    {
        return static::fromTotalSeconds(self::TOTAL_SECONDS_MIN);
    }

    /**
     * Makes a new `Offset` with the specified hours, minutes and
     * seconds. The time units must be within their valid range, and
     * the resulting offset must be in the range -15:00 to +15:00,
     * otherwise an exception will be thrown.
     *
     * All parameters are optional and, if not specified, will take
     * their UTC value (00:00:00).
     *
     * @param int $hours the amount of hours, from -15 to 15
     * @param int $minutes the amount of minutes, from -59 to 59
     * @param int $seconds the amount of seconds, from -59 to 59
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function of(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        Validator::range('hours', $hours, self::HOURS_MIN, self::HOURS_MAX);
        Validator::range('minutes', $minutes, self::MINUTES_MIN, self::MINUTES_MAX);
        Validator::range('seconds', $seconds, self::SECONDS_MIN, self::SECONDS_MAX);

        return static::fromTotalSeconds(($hours * 3600) + ($minutes * 60) + $seconds);
    }

    /**
     * Makes a new `Offset` with the specified total number of minutes.
     * The resulting offset must be in the range -15:00 to +15:00.
     *
     * @param int $minutes total number of minutes, from -900 to 900
     *
     * @throws OutOfRangeException if the total is not in the required range
     */
    public static function fromTotalMinutes(int $minutes): static
    {
        Validator::range('minutes', $minutes, self::TOTAL_MINUTES_MIN, self::TOTAL_MINUTES_MAX);

        return static::fromTotalSeconds($minutes * 60);
    }

    /**
     * Makes a new `Offset` with the specified total number of seconds.
     * The resulting offset must be in the range -15:00 to +15:00.
     *
     * @param int $seconds total number of seconds, from -54,000 to 54,000
     *
     * @throws OutOfRangeException if the total is not in the required range
     */
    public static function fromTotalSeconds(int $seconds): static
    {
        Validator::range('seconds', $seconds, self::TOTAL_SECONDS_MIN, self::TOTAL_SECONDS_MAX);

        /** @psalm-suppress PropertyTypeCoercion */
        return self::$offsets[static::class][$seconds] ??= new static($seconds);
    }

    /**
     * Makes a new `Offset` from a text string using a specific format.
     * It also accepts a list of formats.
     *
     * If the format is not specified, the ISO 8601 offset format will
     * be used (`%R%H:%I`).
     *
     * The `Offset` is not returned directly, but a result that will
     * contain the time if no error was found, or an exception if
     * something went wrong.
     *
     * @param string|array<int, string> $format
     *
     * @return Ok<static>|Error<ParseException|OutOfRangeException>
     *
     * @throws InvalidArgumentException if an empty list of formats is passed
     */
    public static function parse(
        string $string,
        string|array $format = self::ISO8601,
    ): Ok|Error {
        /** @var array<int, string> $formats */
        $formats = [];

        if (\is_array($format)) {
            if (0 === \count($format)) {
                throw new InvalidArgumentException('At least one format must be passed');
            }
            $formats = $format;
            $format = \reset($formats);
        }

        $result = self::parseSimple($string, $format);

        if ($result->isOk()) {
            return $result;
        }

        if (1 < \count($formats)) {
            while ($fmt = \next($formats)) {
                $r = self::parseSimple($string, $fmt);

                if ($r->isOk()) {
                    return $r;
                }
            }
        }

        return $result;
    }

    /**
     * @return Ok<static>|Error<ParseException|OutOfRangeException>
     */
    private static function parseSimple(
        string $string,
        string $format,
    ): Ok|Error {
        $pattern = \preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: static fn (array $matches) => match ($matches[1]) {
                '%' => '%',
                'R' => '(?P<sign>[+-])',
                'r' => '(?P<sign>\-?)',
                'H' => '(?P<hours>[0-9]{2,})',
                'h' => '(?P<hours>[0-9]+)',
                'I' => '(?P<minutes>[0-9]{2,})',
                'i' => '(?P<minutes>[0-9]+)',
                'S' => '(?P<seconds>[0-9]{2,})',
                's' => '(?P<seconds>[0-9]+)',
                default => $matches[0],
            },
            subject: \preg_quote($format, '/')
        );

        if (!\is_string($pattern)
            || !\preg_match("/^{$pattern}$/", $string, $matches)) {
            return Error::withException(new ParseException($string, $format));
        }

        $sign = match ($matches['sign'] ?? '') {
            '-' => -1,
            default => 1,
        };

        try {
            $offset = static::of(
                hours: $sign * (int) ($matches['hours'] ?? 0),
                minutes: $sign * (int) ($matches['minutes'] ?? 0),
                seconds: $sign * (int) ($matches['seconds'] ?? 0),
            );
        } catch (OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($offset);
    }

    /**
     * Makes a new `Offset` from a text with the ISO 8601 offset
     * format (e.g. `'+02:30'`).
     *
     * The offset is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function fromIso8601(string $string): static
    {
        $matches = [];

        if (!\preg_match(self::ISO8601_PATTERN, $string, $matches)) {
            throw new ParseException($string, self::ISO8601);
        }

        $sign = match ($matches['sign']) {
            '-' => -1,
            default => 1,
        };

        return static::of(
            hours: $sign * (int) $matches['hours'],
            minutes: $sign * (int) $matches['minutes'],
            seconds: $sign * (int) ($matches['seconds'] ?? 0),
        );
    }

    /**
     * Makes a new `Offset` from a text with the RFC 2822 offset
     * format (e.g. `'+0230'`).
     *
     * The offset is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function fromRfc2822(string $string): static
    {
        $matches = [];

        if (!\preg_match(self::RFC2822_PATTERN, $string, $matches)) {
            throw new ParseException($string, self::RFC2822);
        }

        $sign = match ($matches['sign']) {
            '-' => -1,
            default => 1,
        };

        return static::of(
            hours: $sign * (int) $matches['hours'],
            minutes: $sign * (int) $matches['minutes'],
            seconds: $sign * (int) ($matches['seconds'] ?? 0),
        );
    }

    /**
     * Makes a new `Offset` from a text with the RFC 3339 offset
     * format (e.g. `'+02:30'`).
     *
     * The offset is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function fromRfc3339(string $string): static
    {
        return static::fromIso8601($string);
    }

    /**
     * Makes a new `Offset` from a text with the SQL offset format
     * (e.g. `'+02:30'`).
     *
     * The offset is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public static function fromSql(string $string): static
    {
        return static::fromIso8601($string);
    }

    /**
     * Formats this offset using the specified format.
     *
     * If the format is not specified, the ISO 8601 offset format will
     * be used (`%R%H:%I`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = self::ISO8601): Ok|Error
    {
        $string = \preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: fn (array $matches) => match ($matches[1]) {
                '%' => '%',
                'R' => ($this->isNegative()) ? '-' : '+',
                'r' => ($this->isNegative()) ? '-' : '',
                'H' => \sprintf('%02d', \abs($this->hours())),
                'h' => (string) \abs($this->hours()),
                'I' => \sprintf('%02d', \abs($this->minutes())),
                'i' => (string) \abs($this->minutes()),
                'S' => \sprintf('%02d', \abs($this->seconds())),
                's' => (string) \abs($this->seconds()),
                default => $matches[0],
            },
            subject: $format
        );

        if (!\is_string($string)) {
            return Error::withException(new FormatException($format));
        }

        return Ok::withValue($string);
    }

    /**
     * Formats this offset using the specified format.
     *
     * If the format is not specified, the ISO 8601 offset format will
     * be used (`%R%H:%I`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws FormatException
     */
    public function formatted(string $format = self::ISO8601): string
    {
        return $this->format($format)->orFail();
    }

    /**
     * Formats this offset with the ISO 8601 offset format (e.g.
     * `'+02:30'`).
     * By default, adds seconds if they are non-zero (for example
     * `'+02:30:45'`). To always add them, set `$seconds` to true. To
     * never add them, set `$seconds` to false.
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @return non-empty-string
     */
    public function toIso8601(?bool $seconds = null): string
    {
        $string = \sprintf(
            '%s%02d:%02d',
            (0 > $this->value) ? '-' : '+',
            \abs($this->hours()),
            \abs($this->minutes())
        );

        if (true === $seconds
            || (null === $seconds && $this->seconds())) {
            $string .= \sprintf(
                ':%02d',
                \abs($this->seconds())
            );
        }

        /** @var non-empty-string */
        return $string;
    }

    /**
     * Formats this offset with the RFC 2822 offset format (e.g.
     * `'+0230'`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc2822(): string
    {
        return \sprintf(
            '%s%02d%02d',
            (0 > $this->value) ? '-' : '+',
            \abs($this->hours()),
            \abs($this->minutes())
        );
    }

    /**
     * Formats this offset with the RFC 3339 offset format (e.g.
     * `'+02:30'`).
     *
     * By default, adds seconds if they are non-zero (for example
     * `'+02:30:45'`). To always add them, set `$seconds` to true. To
     * never add them, set `$seconds` to false.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toRfc3339(?bool $seconds = null): string
    {
        return $this->toIso8601($seconds);
    }

    /**
     * Formats this offset with the SQL offset format (e.g. `'+02:30'`).
     *
     * By default, adds seconds if they are non-zero (for example
     * `'+02:30:45'`). To always add them, set `$seconds` to true. To
     * never add them, set `$seconds` to false.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toSql(?bool $seconds = null): string
    {
        return $this->toIso8601($seconds);
    }

    /**
     * Returns a fixed `TimeZone` with this offset.
     */
    public function toTimeZone(): TimeZone
    {
        return TimeZone::of($this->toIso8601(false));
    }

    /**
     * Returns the amount of hours.
     *
     * @return int<-15, 15>
     */
    public function hours(): int
    {
        /** @var int<-15, 15> */
        return \intdiv($this->value, 3600);
    }

    /**
     * Returns the amount of minutes.
     *
     * @return int<-59, 59>
     */
    public function minutes(): int
    {
        /** @var int<-59, 59> */
        return $this->totalMinutes() % 60;
    }

    /**
     * Returns the amount of seconds.
     *
     * @return int<-59, 59>
     */
    public function seconds(): int
    {
        /** @var int<-59, 59> */
        return $this->value % 60;
    }

    /**
     * Returns the total number of minutes.
     *
     * @return int<-900, 900>
     */
    public function totalMinutes(): int
    {
        /** @var int<-900, 900> */
        return \intdiv($this->value, 60);
    }

    /**
     * Returns the total number of seconds.
     *
     * @return int<-54000, 54000>
     */
    public function totalSeconds(): int
    {
        /** @var int<-54000, 54000> */
        return $this->value;
    }

    /**
     * Compares this offset to another offset.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * offset is less than, equal to, or greater than the given offset.
     */
    public function compareTo(self $that): int
    {
        return ($this->value <=> $that->totalSeconds());
    }

    /**
     * Checks if the given offset belongs to the same class and has
     * the same value as this offset.
     */
    public function is(self $that): bool
    {
        return $this::class === $that::class
            && $this->value === $that->value;
    }

    /**
     * Checks if the given offset belongs to another class or has a
     * different value than this offset.
     */
    public function isNot(self $that): bool
    {
        return $this::class !== $that::class
            || $this->value !== $that->value;
    }

    /**
     * Checks if the given offset has the same value as this offset.
     */
    public function isEqual(self $that): bool
    {
        return ($this->value === $that->totalSeconds());
    }

    /**
     * Checks if the given offset has a different value from this
     * offset.
     */
    public function isNotEqual(self $that): bool
    {
        return ($this->value !== $that->totalSeconds());
    }

    /**
     * Checks if this offset is greater than the specified offset.
     */
    public function isGreater(self $that): bool
    {
        return ($this->value > $that->totalSeconds());
    }

    /**
     * Checks if this offset is greater than or equal to the specified
     * offset.
     */
    public function isGreaterOrEqual(self $that): bool
    {
        return ($this->value >= $that->totalSeconds());
    }

    /**
     * Checks if this offset is less than the specified offset.
     */
    public function isLess(self $that): bool
    {
        return ($this->value < $that->totalSeconds());
    }

    /**
     * Checks if this offset is less than or equal to the specified
     * offset.
     */
    public function isLessOrEqual(self $that): bool
    {
        return ($this->value <= $that->totalSeconds());
    }

    /**
     * Checks if this offset is greater than zero.
     */
    public function isPositive(): bool
    {
        return (0 < $this->value);
    }

    /**
     * Checks if this offset is less than zero.
     */
    public function isNegative(): bool
    {
        return (0 > $this->value);
    }

    /**
     * Checks if this offset is equal to zero.
     */
    public function isZero(): bool
    {
        return (0 === $this->value);
    }

    /**
     * Returns a copy of this offset with the specified amount of
     * hours, minutes and seconds added.
     *
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function plus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        $totalSeconds = \intadd(
            \intmul($hours, 3600),
            \intmul($minutes, 60),
            $seconds,
        );

        return static::fromTotalSeconds(\intadd($this->value, $totalSeconds));
    }

    /**
     * Returns a copy of this offset with the specified amount of
     * hours, minutes and seconds subtracted.
     *
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function minus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        $totalSeconds = \intadd(
            \intmul($hours, 3600),
            \intmul($minutes, 60),
            $seconds,
        );

        return static::fromTotalSeconds(\intsub($this->value, $totalSeconds));
    }

    /**
     * Returns a copy of this offset with the specified hours, minutes
     * and seconds.
     *
     * @param ?int $hours the amount of hours, from -15 to 15
     * @param ?int $minutes the amount of minutes, from -59 to 59
     * @param ?int $seconds the amount of seconds, from -59 to 59
     *
     * @throws OutOfRangeException if the value of any unit is out of range
     */
    public function with(
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
    ): static {
        return static::of(
            $hours ?? $this->hours(),
            $minutes ?? $this->minutes(),
            $seconds ?? $this->seconds(),
        );
    }

    /**
     * Makes a copy of this offset with the specified amount of hours,
     * minutes and seconds added.
     *
     * It works the same as the {@see plus()} method, but returns a
     * result instead of the new offset.
     *
     * The result will contain the new offset if no error was found,
     * or an exception if something went wrong.
     *
     * @return Ok<static>|Error<ArithmeticError|OutOfRangeException>
     */
    public function add(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): Ok|Error {
        try {
            $period = $this->plus($hours, $minutes, $seconds);
        } catch (ArithmeticError|OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this offset with the specified amount of hours,
     * minutes and seconds subtracted.
     *
     * It works the same as the {@see minus()} method, but returns a
     * result instead of the new offset.
     *
     * The result will contain the new offset if no error was found,
     * or an exception if something went wrong.
     *
     * @return Ok<static>|Error<ArithmeticError|OutOfRangeException>
     */
    public function subtract(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): Ok|Error {
        try {
            $period = $this->minus($hours, $minutes, $seconds);
        } catch (ArithmeticError|OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this offset with the specified hours, minutes
     * and seconds.
     *
     * It works the same as the {@see with()} method, but returns a
     * result instead of the new offset.
     *
     * The result will contain the new offset if no error was found,
     * or an exception if something went wrong.
     *
     * @param ?int $hours the amount of hours, from -15 to 15
     * @param ?int $minutes the amount of minutes, from -59 to 59
     * @param ?int $seconds the amount of seconds, from -59 to 59
     *
     * @return Ok<static>|Error<OutOfRangeException>
     */
    public function copy(
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
    ): Ok|Error {
        try {
            $time = $this->with(
                $hours, $minutes, $seconds,
            );
        } catch (OutOfRangeException $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }
}
