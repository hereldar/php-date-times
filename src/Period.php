<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateInterval as NativeDateInterval;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\Copyable;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Multipliable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use TypeError;

/**
 * An amount of time in the ISO-8601 calendar system, such as 2 months,
 * 3 days and 12 hours.
 *
 * This class models a quantity or amount of time in terms of years,
 * months, days, hours, minutes, seconds and microseconds.
 *
 * Each time unit is stored individually, and is retrieved as
 * specified when creating the period.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class Period implements Formattable, Stringable, Copyable, Summable, Multipliable
{
    final public const ISO8601 = 'P%yY%mM%dDT%hH%iM%s%fS';

    private const ISO8601_PATTERN = <<<'REGEX'
        /
            (?P<sign>[+-])?
            P
            (?:
                (?P<years>[+-]?[0-9]+)
                Y
            )?
            (?:
                (?P<months>[+-]?[0-9]+)
                M
            )?
            (?:
                (?P<weeks>[+-]?[0-9]+)
                W
            )?
            (?:
                (?P<days>[+-]?[0-9]+)
                D
            )?
            (?:T
                (?:
                    (?P<hours>[+-]?[0-9]+)
                    H
                )?
                (?:
                    (?P<minutes>[+-]?[0-9]+)
                    M
                )?
                (?:
                    (?P<seconds>[+-]?[0-9]+)
                    (?:
                        \.
                        (?P<microseconds>[0-9]{1,6})
                    )?
                    S
                )?
            )?
        /xS
        REGEX;

    private const FORMAT_PATTERN = '/%([%a-zA-Z])/';

    /**
     * @internal
     */
    private function __construct(
        private readonly int $years = 0,
        private readonly int $months = 0,
        private readonly int $days = 0,
        private readonly int $hours = 0,
        private readonly int $minutes = 0,
        private readonly int $seconds = 0,
        private readonly int $microseconds = 0,
    ) {}

    /**
     * Outputs this period as a `string`, using the default format of
     * the class.
     */
    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    /**
     * An empty period (P0S).
     */
    public static function zero(): static
    {
        return new static();
    }

    /**
     * Makes a `Period` consisting of the number of years, months,
     * days, hours, minutes, seconds and microseconds between two
     * time points.
     *
     * Both the start and the end points must store the same time
     * units. This means that a `Date` cannot be passed next to a
     * `Time`, otherwise an exception will be thrown.
     *
     * The period can be negative if the end is before the start. The
     * negative sign shall be the same for each time unit. To ensure a
     * positive period is obtained call {@see abs()} on the result.
     *
     * @throws TypeError if the given objects store different time units
     *
     * @psalm-suppress PossiblyInvalidArgument
     */
    public static function between(
        DateTime|LocalDateTime|LocalDate|LocalTime $startInclusive,
        DateTime|LocalDateTime|LocalDate|LocalTime $endExclusive,
    ): static {
        if ($startInclusive instanceof DateTime) {
            /** @phpstan-ignore-next-line */
            return static::betweenDateTimes($startInclusive, $endExclusive);
        }

        if ($startInclusive instanceof LocalDateTime) {
            /** @phpstan-ignore-next-line */
            return static::betweenLocalDateTimes($startInclusive, $endExclusive);
        }

        if ($startInclusive instanceof LocalDate) {
            /** @phpstan-ignore-next-line */
            return static::betweenLocalDates($startInclusive, $endExclusive);
        }

        /** @phpstan-ignore-next-line */
        return static::betweenLocalTimes($startInclusive, $endExclusive);
    }

    protected static function betweenDateTimes(
        DateTime $startInclusive,
        DateTime $endExclusive,
    ): static {
        $a = $startInclusive->toNative();
        $b = $endExclusive->toNative();

        return static::fromNative($a->diff($b));
    }

    protected static function betweenLocalDateTimes(
        LocalDateTime $startInclusive,
        LocalDateTime $endExclusive,
    ): static {
        $a = $startInclusive->toNative();
        $b = $endExclusive->toNative();

        return static::fromNative($a->diff($b));
    }

    protected static function betweenLocalDates(
        LocalDate $startInclusive,
        LocalDate $endExclusive,
    ): static {
        $a = $startInclusive->toNative();
        $b = $endExclusive->toNative();

        return static::fromNative($a->diff($b));
    }

    protected static function betweenLocalTimes(
        LocalTime $startInclusive,
        LocalTime $endExclusive,
    ): static {
        $a = $startInclusive->toNative();
        $b = $endExclusive->toNative();

        return static::fromNative($a->diff($b));
    }

    /**
     * Makes a new `Period` with the specified years, months, days,
     * hours, minutes, seconds and microseconds.
     *
     * All parameters are optional and, if not specified, will be set
     * to zero.
     *
     * No normalization is performed.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify units other than years, months, days, hours, minutes,
     * seconds and microseconds, since only the order of the seven
     * first parameters is guaranteed.
     *
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public static function of(
        int $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $microseconds = 0,
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): static {
        $y = intadd($years, intmul($decades, 10), intmul($centuries, 100), intmul($millennia, 1_000));
        $m = intadd($months, intmul($quarters, 3));
        $d = intadd($days, intmul($weeks, 7));
        $h = $hours;
        $i = $minutes;
        $s = $seconds;
        $f = intadd($microseconds, intmul($milliseconds, 1_000));

        return new static($y, $m, $d, $h, $i, $s, $f);
    }

    /**
     * Makes a new `Period` from a text string using a specific format.
     * It also accepts a list of formats.
     *
     * If the format is not specified, the ISO 8601 period format will
     * be used (`P%yY%mM%dDT%hH%iM%s%fS`).
     *
     * The `Period` is not returned directly, but a result that will
     * contain the time if no error was found, or an exception if
     * something went wrong.
     *
     * @param string|array<int, string> $format
     *
     * @throws InvalidArgumentException if an empty list of formats is passed
     *
     * @return Ok<static>|Error<ParseException|ArithmeticError>
     */
    public static function parse(
        string $string,
        string|array $format = Period::ISO8601,
    ): Ok|Error {
        if ($format === self::ISO8601) {
            /** @var Ok<static> */
            return Ok::withValue(static::fromIso8601($string));
        }

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

        $result = self::parseSimple($string, $format);

        if ($result->isOk()) {
            return $result;
        }

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
                $r = self::parseSimple($string, $fmt);

                if ($r->isOk()) {
                    return $r;
                }
            }
        }

        return $result;
    }

    /**
     * @return Ok<static>|Error<ParseException|ArithmeticError>
     */
    private static function parseSimple(
        string $string,
        string $format,
    ): Ok|Error {
        $pattern = preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: static fn(array $matches) => match ($matches[1]) {
                '%' => '%',
                'R' => '(?P<sign>[+-])',
                'r' => '(?P<sign>\-?)',
                'Y' => '(?P<years>[+-]?[0-9]{4,})',
                'y' => '(?P<years>[+-]?[0-9]+)',
                'M' => '(?P<months>[+-]?[0-9]{2,})',
                'm' => '(?P<months>[+-]?[0-9]+)',
                'W' => '(?P<weeks>[+-]?[0-9]{2,})',
                'w' => '(?P<weeks>[+-]?[0-9]+)',
                'D', 'E' => '(?P<days>[+-]?[0-9]{2,})',
                'd', 'e' => '(?P<days>[+-]?[0-9]+)',
                'H' => '(?P<hours>[+-]?[0-9]{2,})',
                'h' => '(?P<hours>[+-]?[0-9]+)',
                'I' => '(?P<minutes>[+-]?[0-9]{2,})',
                'i' => '(?P<minutes>[+-]?[0-9]+)',
                'S' => '(?P<seconds>[+-]?[0-9]{2,})',
                's' => '(?P<seconds>[+-]?[0-9]+)',
                'F' => '[.,](?P<microseconds>[+-]?[0-9]{6})',
                'f' => '[.,](?P<decimalSeconds>[+-]?[0-9]{1,6})',
                'U' => '(?P<microseconds>[+-]?[0-9]{6,})',
                'u' => '(?P<microseconds>[+-]?[0-9]+)',
                'V' => '(?P<milliseconds>[+-]?[0-9]{3,})',
                'v' => '(?P<milliseconds>[+-]?[0-9]+)',
                default => $matches[0],
            },
            subject: preg_quote($format, '/')
        );

        if (!is_string($pattern)
            || !preg_match("/^{$pattern}$/", $string, $matches)) {
            return Error::withException(new ParseException($string, $format));
        }

        $sign = match ($matches['sign'] ?? '') {
            '-' => -1,
            default => 1,
        };

        unset($matches['sign']);

        $arguments = [];
        foreach ($matches as $key => $value) {
            if ($value && !is_int($key)) {
                if ($key === 'decimalSeconds') {
                    $key = 'microseconds';
                    $value = str_pad($value, 6, '0');
                }
                $arguments[$key] = $sign * (int) $value;
            }
        }

        try {
            $period = static::of(...$arguments);
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a new `Period` from a text with the ISO 8601 period
     * format (e.g. `'P2DT30M'`).
     *
     * The period is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws ParseException if the text cannot be parsed
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public static function fromIso8601(string $string): static
    {
        $matches = [];

        if (!preg_match(self::ISO8601_PATTERN, $string, $matches)) {
            throw new ParseException($string, self::ISO8601);
        }

        $sign = match ($matches['sign'] ?? '') {
            '-' => -1,
            default => 1,
        };
        $seconds = (int) ($matches['seconds'] ?? 0);
        $microseconds = (int) str_pad($matches['microseconds'] ?? '', 6, '0');

        if ($seconds < 0) {
            $microseconds *= -1;
        }

        return static::of(
            years: $sign * (int) ($matches['years'] ?? 0),
            months: $sign * (int) ($matches['months'] ?? 0),
            weeks: $sign * (int) ($matches['weeks'] ?? 0),
            days: $sign * (int) ($matches['days'] ?? 0),
            hours: $sign * (int) ($matches['hours'] ?? 0),
            minutes: $sign * (int) ($matches['minutes'] ?? 0),
            seconds: $sign * $seconds,
            microseconds: $sign * $microseconds,
        );
    }

    /**
     * Makes a new `Period` from a native `DateInterval`.
     */
    public static function fromNative(
        NativeDateInterval $interval,
    ): static {
        $sign = ($interval->invert) ? -1 : 1;

        return new static(
            $sign * $interval->y,
            $sign * $interval->m,
            $sign * $interval->d,
            $sign * $interval->h,
            $sign * $interval->i,
            $sign * $interval->s,
            $sign * ((int) round($interval->f * 1_000_000.0)),
        );
    }

    /**
     * Formats this period using the specified format.
     *
     * If the format is not specified, the ISO 8601 period format will
     * be used (`P%yY%mM%dDT%hH%iM%s%fS`).
     *
     * The text is not returned directly, but a result that will
     * contain the text if no error was found, or an exception if
     * something went wrong.
     *
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format = Period::ISO8601): Ok|Error
    {
        if ($format === self::ISO8601) {
            return Ok::withValue($this->toIso8601());
        }

        $string = preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: fn(array $matches) => match ($matches[1]) {
                '%' => '%',
                'Y' => sprintf('%04d', $this->years),
                'y' => (string) $this->years,
                'M' => sprintf('%02d', $this->months),
                'm' => (string) $this->months,
                'W' => sprintf('%02d', intdiv($this->days, 7)),
                'w' => (string) intdiv($this->days, 7),
                'D' => sprintf('%02d', $this->days),
                'd' => (string) $this->days,
                'E' => sprintf('%02d', $this->days % 7),
                'e' => (string) ($this->days % 7),
                'H' => sprintf('%02d', $this->hours),
                'h' => (string) $this->hours,
                'I' => sprintf('%02d', $this->minutes),
                'i' => (string) $this->minutes,
                'S' => sprintf('%02d', $this->seconds),
                's' => (string) $this->seconds,
                'F' => ($this->microseconds) ? sprintf('.%06d', $this->microseconds) : '',
                'f' => ($this->microseconds) ? rtrim(sprintf('.%06d', $this->microseconds), '0') : '',
                'U' => sprintf('%06d', $this->microseconds),
                'u' => (string) $this->microseconds,
                'V' => sprintf('%03d', intdiv($this->microseconds, 1_000)),
                'v' => (string) intdiv($this->microseconds, 1_000),
                default => $matches[0],
            },
            subject: $format
        );

        if (!is_string($string)) {
            return Error::withException(new FormatException($format));
        }

        return Ok::withValue($string);
    }

    /**
     * Formats this period using the specified format.
     *
     * If the format is not specified, the ISO 8601 period format will
     * be used (`P%yY%mM%dDT%hH%iM%s%fS`).
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     *
     * @throws FormatException
     */
    public function formatted(string $format = Period::ISO8601): string
    {
        return $this->format($format)->orFail();
    }

    /**
     * Formats this period with the ISO 8601 period format (e.g.
     * `'P2DT30M'`).
     *
     * Units equal to zero are not included in the resulting text.
     *
     * The text is returned directly if no error is found, otherwise
     * an exception is thrown.
     */
    public function toIso8601(): string
    {
        if ($this->isZero()) {
            return 'PT0S';
        }

        if ($this->isNegative()) {
            $string = '-P';
            $sign = -1;
        } else {
            $string = 'P';
            $sign = 1;
        }

        $y = $sign * $this->years;
        $m = $sign * $this->months;
        $d = $sign * $this->days;
        $h = $sign * $this->hours;
        $i = $sign * $this->minutes;
        $s = $sign * $this->seconds;
        $f = $sign * $this->microseconds;

        if ($y) {
            $string .= "{$y}Y";
        }

        if ($m) {
            $string .= "{$m}M";
        }

        if ($d) {
            $string .= "{$d}D";
        }

        if (!$h && !$i && !$s && !$f) {
            return $string;
        }

        $string .= 'T';

        if ($h) {
            $string .= "{$h}H";
        }

        if ($i) {
            $string .= "{$i}M";
        }

        if ($f) {
            $microseconds = rtrim(sprintf('%06d', $f), '0');
            $string .= "{$s}.{$microseconds}S";
        } elseif ($s) {
            $string .= "{$s}S";
        }

        return $string;
    }

    /**
     * Returns a native `DateInterval` with the values of this period.
     */
    public function toNative(): NativeDateInterval
    {
        $interval = new NativeDateInterval('PT0S');

        $sign = ($this->isNegative()) ? -1 : 1;

        $interval->y = $sign * $this->years;
        $interval->m = $sign * $this->months;
        $interval->d = $sign * $this->days;
        $interval->h = $sign * $this->hours;
        $interval->i = $sign * $this->minutes;
        $interval->s = $sign * $this->seconds;
        $interval->f = (float) (($sign * $this->microseconds) / 1_000_000);

        if ($sign === -1) {
            $interval->invert = 1;
        }

        return $interval;
    }

    /**
     * Returns the amount of years.
     */
    public function years(): int
    {
        return $this->years;
    }

    /**
     * Returns the amount of months.
     */
    public function months(): int
    {
        return $this->months;
    }

    /**
     * Returns the amount of days.
     */
    public function days(): int
    {
        return $this->days;
    }

    /**
     * Returns the amount of hours.
     */
    public function hours(): int
    {
        return $this->hours;
    }

    /**
     * Returns the amount of minutes.
     */
    public function minutes(): int
    {
        return $this->minutes;
    }

    /**
     * Returns the amount of seconds.
     */
    public function seconds(): int
    {
        return $this->seconds;
    }

    /**
     * Returns the amount of microseconds.
     */
    public function microseconds(): int
    {
        return $this->microseconds;
    }

    /**
     * Compares this period to another period.
     *
     * Returns a negative integer, zero, or a positive integer as this
     * period is less than, equal to, or greater than the given period.
     *
     * Values are normalized before comparison, so a period of "15
     * Months" is considered equal to a period of "1 Year and 3 Months".
     *
     * @see normalized()
     */
    public function compareTo(Period $that): int
    {
        $a = $this->normalized();
        $b = $that->normalized();

        if ($result = ($a->years <=> $b->years())) {
            return $result;
        }

        if ($result = ($a->months <=> $b->months())) {
            return $result;
        }

        if ($result = ($a->days <=> $b->days())) {
            return $result;
        }

        if ($result = ($a->hours <=> $b->hours())) {
            return $result;
        }

        if ($result = ($a->minutes <=> $b->minutes())) {
            return $result;
        }

        if ($result = ($a->seconds <=> $b->seconds())) {
            return $result;
        }

        return ($a->microseconds <=> $b->microseconds());
    }

    /**
     * Checks if the given period belongs to the same class and has
     * the same values as this period.
     */
    public function is(Period $that): bool
    {
        return $this::class === $that::class
            && $this->microseconds === $that->microseconds
            && $this->seconds === $that->seconds
            && $this->minutes === $that->minutes
            && $this->hours === $that->hours
            && $this->days === $that->days
            && $this->months === $that->months
            && $this->years === $that->years;
    }

    /**
     * Checks if the given period belongs to another class or has
     * different values than this period.
     */
    public function isNot(Period $that): bool
    {
        return $this::class !== $that::class
            || $this->microseconds !== $that->microseconds
            || $this->seconds !== $that->seconds
            || $this->minutes !== $that->minutes
            || $this->hours !== $that->hours
            || $this->days !== $that->days
            || $this->months !== $that->months
            || $this->years !== $that->years;
    }

    /**
     * Checks if the given period has the same values as this period.
     */
    public function isEqual(Period $that): bool
    {
        return $this->microseconds === $that->microseconds()
            && $this->seconds === $that->seconds()
            && $this->minutes === $that->minutes()
            && $this->hours === $that->hours()
            && $this->days === $that->days()
            && $this->months === $that->months()
            && $this->years === $that->years();
    }

    /**
     * Checks if the given period has values different from those of
     * this period.
     */
    public function isNotEqual(Period $that): bool
    {
        return $this->microseconds !== $that->microseconds()
            || $this->seconds !== $that->seconds()
            || $this->minutes !== $that->minutes()
            || $this->hours !== $that->hours()
            || $this->days !== $that->days()
            || $this->months !== $that->months()
            || $this->years !== $that->years();
    }

    /**
     * Checks if the given period has the same normalized values as
     * this period.
     *
     * @see normalized()
     */
    public function isSimilar(Period $that): bool
    {
        return (0 === $this->compareTo($that));
    }

    /**
     * Checks if the given period has normalized values different from
     * those of this period.
     *
     * Values are normalized before comparison.
     *
     * @see normalized()
     */
    public function isNotSimilar(Period $that): bool
    {
        return (0 !== $this->compareTo($that));
    }

    /**
     * Checks if this period is greater than the specified period.
     *
     * Values are normalized before comparison.
     *
     * @see normalized()
     */
    public function isGreater(Period $that): bool
    {
        return (0 < $this->compareTo($that));
    }

    /**
     * Checks if this period is greater than or equal to the specified
     * period.
     *
     * Values are normalized before comparison.
     *
     * @see normalized()
     */
    public function isGreaterOrEqual(Period $that): bool
    {
        return (0 <= $this->compareTo($that));
    }

    /**
     * Checks if this period is less than the specified period.
     *
     * Values are normalized before comparison.
     *
     * @see normalized()
     */
    public function isLess(Period $that): bool
    {
        return (0 > $this->compareTo($that));
    }

    /**
     * Checks if this period is less than or equal to the specified
     * period.
     *
     * Values are normalized before comparison.
     *
     * @see normalized()
     */
    public function isLessOrEqual(Period $that): bool
    {
        return (0 >= $this->compareTo($that));
    }

    /**
     * Checks if this period has any value greater than zero.
     */
    public function hasPositiveValues(): bool
    {
        return $this->microseconds > 0
            || $this->seconds > 0
            || $this->minutes > 0
            || $this->hours > 0
            || $this->days > 0
            || $this->months > 0
            || $this->years > 0;
    }

    /**
     * Checks if this period has any value less than zero.
     */
    public function hasNegativeValues(): bool
    {
        return $this->microseconds < 0
            || $this->seconds < 0
            || $this->minutes < 0
            || $this->hours < 0
            || $this->days < 0
            || $this->months < 0
            || $this->years < 0;
    }

    /**
     * Checks if this period has any value greater than zero, and if
     * none of its values is less than zero.
     */
    public function isPositive(): bool
    {
        return $this->hasPositiveValues()
            && !$this->hasNegativeValues();
    }

    /**
     * Checks if this period has any value less than zero, and if none
     * of its values is greater than zero.
     */
    public function isNegative(): bool
    {
        return $this->hasNegativeValues()
            && !$this->hasPositiveValues();
    }

    /**
     * Checks if all values of this period are zero.
     */
    public function isZero(): bool
    {
        return !$this->microseconds
            && !$this->seconds
            && !$this->minutes
            && !$this->hours
            && !$this->days
            && !$this->months
            && !$this->years;
    }

    /**
     * Returns a copy of this period with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * added.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify units other than years, months, days, hours, minutes,
     * seconds and microseconds, since only the order of the seven
     * first parameters is guaranteed.
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
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($years)) {
            $period = static::of(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks, $milliseconds,
            );
        } elseif (
            !$months && !$days
            && !$hours && !$minutes && !$seconds && !$microseconds
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks && !$milliseconds
        ) {
            $period = $years;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        return new static(
            intadd($this->years, $period->years()),
            intadd($this->months, $period->months()),
            intadd($this->days, $period->days()),
            intadd($this->hours, $period->hours()),
            intadd($this->minutes, $period->minutes()),
            intadd($this->seconds, $period->seconds()),
            intadd($this->microseconds, $period->microseconds()),
        );
    }

    /**
     * Returns a copy of this period with the specified amount of
     * years, months, days, hours, minutes, seconds and microseconds
     * subtracted.
     *
     * WARNING: It is strongly recommended to use named arguments to
     * specify units other than years, months, days, hours, minutes,
     * seconds and microseconds, since only the order of the seven
     * first parameters is guaranteed.
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
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): static {
        if (is_int($years)) {
            $period = static::of(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks, $milliseconds,
            );
        } elseif (
            !$months && !$days
            && !$hours && !$minutes && !$seconds && !$microseconds
            && !$millennia && !$centuries && !$decades
            && !$quarters && !$weeks && !$milliseconds
        ) {
            $period = $years;
        } else {
            throw new InvalidArgumentException(
                'No time units are allowed when a period is passed'
            );
        }

        return new static(
            intsub($this->years, $period->years()),
            intsub($this->months, $period->months()),
            intsub($this->days, $period->days()),
            intsub($this->hours, $period->hours()),
            intsub($this->minutes, $period->minutes()),
            intsub($this->seconds, $period->seconds()),
            intsub($this->microseconds, $period->microseconds()),
        );
    }

    /**
     * Returns a copy of this period with each of its amounts
     * multiplied by the specified number.
     *
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public function multipliedBy(int $multiplicand): static
    {
        return new static(
            intmul($this->years, $multiplicand),
            intmul($this->months, $multiplicand),
            intmul($this->days, $multiplicand),
            intmul($this->hours, $multiplicand),
            intmul($this->minutes, $multiplicand),
            intmul($this->seconds, $multiplicand),
            intmul($this->microseconds, $multiplicand),
        );
    }

    /**
     * Returns a copy of this period with each of its amounts
     * divided by the specified number. The remainder of each
     * division is carried to the next unit.
     *
     * This is an unsafe operation, since the relationships between
     * some units are not exact. The number of days in a month varies
     * from 28 to 31, and some days do not have 24 hours due daylight
     * saving time. However, this operation considers that months have
     * 30 days and days have 24 hours.
     */
    public function dividedBy(int $divisor): static
    {
        $years = $this->years;
        $y = intdiv($years, $divisor);

        $months = $this->months + ($years % $divisor * 12);
        $m = intdiv($months, $divisor);

        $days = $this->days + ($months % $divisor * 30);
        $d = intdiv($days, $divisor);

        $hours = $this->hours + ($days % $divisor * 24);
        $h = intdiv($hours, $divisor);

        $minutes = $this->minutes + ($hours % $divisor * 60);
        $i = intdiv($minutes, $divisor);

        $seconds = $this->seconds + ($minutes % $divisor * 60);
        $s = intdiv($seconds, $divisor);

        $microseconds = $this->microseconds + ($seconds % $divisor * 1_000_000);
        $f = intdiv($microseconds, $divisor);

        return new static($y, $m, $d, $h, $i, $s, $f);
    }

    /**
     * Returns a copy of this period with positive amounts.
     */
    public function abs(): static
    {
        return new static(
            abs($this->years),
            abs($this->months),
            abs($this->days),
            abs($this->hours),
            abs($this->minutes),
            abs($this->seconds),
            abs($this->microseconds),
        );
    }

    /**
     * Returns a copy of this period with each of its amounts negated.
     */
    public function negated(): static
    {
        return new static(
            -$this->years,
            -$this->months,
            -$this->days,
            -$this->hours,
            -$this->minutes,
            -$this->seconds,
            -$this->microseconds,
        );
    }

    /**
     * Returns a copy of this period with each of its amounts
     * divided by the specified number.
     *
     * This is an unsafe operation, since the relationships between
     * some units are not exact. The number of days in a month varies
     * from 28 to 31, and some days do not have 24 hours due daylight
     * saving time. However, this operation considers that months have
     * 30 days and days have 24 hours.
     */
    public function normalized(): static
    {
        $factors = [12, 30, 24, 60, 60, 1_000_000, 0];
        $reversedFactors = [1_000_000, 60, 60, 24, 30, 12, 0];

        $changed = false;

        $y = $this->years;
        $m = $this->months;
        $d = $this->days;
        $h = $this->hours;
        $i = $this->minutes;
        $s = $this->seconds;
        $f = $this->microseconds;

        [$f, $s, $i, $h, $d, $m, $y] = $this->normalizeOverflowedValues(
            [$f, $s, $i, $h, $d, $m, $y],
            $reversedFactors,
            $changed,
        );

        if ($this->hasNegativeValues()
            && $this->hasPositiveValues()) {
            [$y, $m, $d, $h, $i, $s, $f] = $this->normalizeMixedSigns(
                [$y, $m, $d, $h, $i, $s, $f],
                $factors,
                $changed,
            );

            [$f, $s, $i, $h, $d, $m, $y] = $this->normalizeOverflowedValues(
                [$f, $s, $i, $h, $d, $m, $y],
                $reversedFactors,
                $changed,
            );
        }

        if (!$changed) {
            return $this;
        }

        return new static($y, $m, $d, $h, $i, $s, $f);
    }

    /**
     * @param int[] $values
     * @param int[] $factors
     *
     * @return int[]
     */
    private function normalizeOverflowedValues(array $values, array $factors, bool &$changed): array
    {
        $previousValue = 0;
        $previousFactor = 0;

        foreach ($values as $key => &$value) {
            $factor = $factors[$key];

            if ($previousFactor
                && $overflow = intdiv($previousValue, $previousFactor)) {
                $previousValue -= $overflow * $previousFactor;
                $value += $overflow;
                $changed = true;
            }

            $previousValue = &$value;
            $previousFactor = $factor;
        }

        return $values;
    }

    /**
     * @param int[] $values
     * @param int[] $factors
     *
     * @return int[]
     */
    private function normalizeMixedSigns(array $values, array $factors, bool &$changed): array
    {
        $sign = 0;

        $previousValue = 0;
        $previousFactor = 0;

        foreach ($values as $key => &$value) {
            $factor = $factors[$key];

            if (!$sign || !$previousFactor) {
                $sign = ($value <=> 0);
            }

            if ($sign && !$value) {
                $previousFactor *= $factor;
                continue;
            }

            if ($previousFactor) {
                if ($sign === 1) {
                    if ($value < 0) {
                        --$previousValue;
                        $value += $previousFactor;
                        $changed = true;
                    }
                } elseif ($sign === -1) {
                    if ($value > 0) {
                        ++$previousValue;
                        $value -= $previousFactor;
                        $changed = true;
                    }
                }
            }

            $previousValue = &$value;
            $previousFactor = $factor;
        }

        return $values;
    }

    /**
     * Returns a copy of this period with the specified years, months,
     * days, hours, minutes, seconds and microseconds.
     *
     * @throws ArithmeticError if any value exceeds the PHP limits for an integer
     */
    public function with(
        ?int $years = null,
        ?int $months = null,
        ?int $days = null,
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
        ?int $microseconds = null,
    ): static {
        return static::of(
            $years ?? $this->years,
            $months ?? $this->months,
            $days ?? $this->days,
            $hours ?? $this->hours,
            $minutes ?? $this->minutes,
            $seconds ?? $this->seconds,
            $microseconds ?? $this->microseconds
        );
    }

    /**
     * Makes a copy of this period with the specified amount of years,
     * months, days, hours, minutes, seconds and microseconds added.
     *
     * It works the same as the {@see plus()} method, but returns a
     * result instead of the new period.
     *
     * The result will contain the new period if no error was found,
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
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): Ok|Error {
        try {
            $period = $this->plus(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks, $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this period with the specified amount of years,
     * months, days, hours, minutes, seconds and microseconds
     * subtracted.
     *
     * It works the same as the {@see minus()} method, but returns a
     * result instead of the new period.
     *
     * The result will contain the new period if no error was found,
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
        int $millennia = 0,
        int $centuries = 0,
        int $decades = 0,
        int $quarters = 0,
        int $weeks = 0,
        int $milliseconds = 0,
    ): Ok|Error {
        try {
            $period = $this->minus(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
                $millennia, $centuries, $decades,
                $quarters, $weeks, $milliseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this period with each of its amounts multiplied
     * by the specified number.
     *
     * It works the same as the {@see multipliedBy()} method, but
     * returns a result instead of the new period.
     *
     * The result will contain the new period if no error was found,
     * or an exception if something went wrong.
     *
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function multiplyBy(int $multiplicand): Ok|Error
    {
        try {
            $period = $this->multipliedBy($multiplicand);
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this period with each of its amounts divided by
     * the specified number.
     *
     * It works the same as the {@see dividedBy()} method, but returns
     * a result instead of the new period.
     *
     * The result will contain the new period if no error was found,
     * or an exception if something went wrong.
     *
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function divideBy(int $divisor): Ok|Error
    {
        try {
            $period = $this->dividedBy($divisor);
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

    /**
     * Makes a copy of this period with the specified years, months,
     * days, hours, minutes, seconds and microseconds.
     *
     * It works the same as the {@see with()} method, but returns a
     * result instead of the new period.
     *
     * The result will contain the new period if no error was found,
     * or an exception if something went wrong.
     *
     * @return Ok<static>|Error<ArithmeticError>
     */
    public function copy(
        ?int $years = null,
        ?int $months = null,
        ?int $days = null,
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
        ?int $microseconds = null,
    ): Ok|Error {
        try {
            $time = $this->with(
                $years, $months, $days,
                $hours, $minutes, $seconds, $microseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($time);
    }
}
