<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateInterval as NativeDateInterval;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class Period implements IPeriod, Stringable
{
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

    private function __construct(
        private readonly int $years,
        private readonly int $months = 0,
        private readonly int $days = 0,
        private readonly int $hours = 0,
        private readonly int $minutes = 0,
        private readonly int $seconds = 0,
        private readonly int $microseconds = 0,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function of(
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        $y = $years;
        $m = $months;
        $d = $days + ($weeks * 7);
        $h = $hours;
        $i = $minutes;
        $s = $seconds;
        $f = $microseconds + ($milliseconds * 1_000);

        return new static($y, $m, $d, $h, $i, $s, $f);
    }

    public static function zero(): static
    {
        return new static(0);
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string $format = IPeriod::ISO8601,
    ): Ok|Error {
        if ($format === IPeriod::ISO8601) {
            /** @var Ok<static> */
            return Ok::withValue(static::fromIso8601($string));
        }

        $pattern = preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: static fn (array $matches) => match ($matches[1]) {
                '%' => '%',
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

        $arguments = [];
        foreach ($matches as $key => $value) {
            if ($value && !is_int($key)) {
                if ($key === 'decimalSeconds') {
                    $key = 'microseconds';
                    $value = str_pad($value, 6, '0');
                }
                $arguments[$key] = (int) $value;
            }
        }

        /** @var Ok<static> */
        return Ok::withValue(static::of(...$arguments));
    }

    public static function fromIso8601(string $string): static
    {
        $matches = [];

        if (!preg_match(self::ISO8601_PATTERN, $string, $matches)) {
            throw new ParseException($string, IPeriod::ISO8601);
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

    public function format(string $format = IPeriod::ISO8601): Ok|Error
    {
        if ($format === IPeriod::ISO8601) {
            return Ok::withValue($this->toIso8601());
        }

        $string = preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: fn (array $matches) => match ($matches[1]) {
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
            $microseconds = rtrim(sprintf("%06d", $f), '0');
            $string .= "{$s}.{$microseconds}S";
        } elseif ($s) {
            $string .= "{$s}S";
        }

        return $string;
    }

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

    public function years(): int
    {
        return $this->years;
    }

    public function months(): int
    {
        return $this->months;
    }

    public function days(): int
    {
        return $this->days;
    }

    public function hours(): int
    {
        return $this->hours;
    }

    public function minutes(): int
    {
        return $this->minutes;
    }

    public function seconds(): int
    {
        return $this->seconds;
    }

    public function microseconds(): int
    {
        return $this->microseconds;
    }

    public function compareTo(IPeriod $that): int
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

    public function is(IPeriod $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->microseconds === $that->microseconds // @phpstan-ignore-line
            && $this->seconds === $that->seconds // @phpstan-ignore-line
            && $this->minutes === $that->minutes // @phpstan-ignore-line
            && $this->hours === $that->hours // @phpstan-ignore-line
            && $this->days === $that->days // @phpstan-ignore-line
            && $this->months === $that->months // @phpstan-ignore-line
            && $this->years === $that->years; // @phpstan-ignore-line
    }

    public function isNot(IPeriod $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->microseconds !== $that->microseconds // @phpstan-ignore-line
            || $this->seconds !== $that->seconds // @phpstan-ignore-line
            || $this->minutes !== $that->minutes // @phpstan-ignore-line
            || $this->hours !== $that->hours // @phpstan-ignore-line
            || $this->days !== $that->days // @phpstan-ignore-line
            || $this->months !== $that->months // @phpstan-ignore-line
            || $this->years !== $that->years; // @phpstan-ignore-line
    }

    public function isEqual(IPeriod $that): bool
    {
        return $this->microseconds === $that->microseconds()
            && $this->seconds === $that->seconds()
            && $this->minutes === $that->minutes()
            && $this->hours === $that->hours()
            && $this->days === $that->days()
            && $this->months === $that->months()
            && $this->years === $that->years();
    }

    public function isNotEqual(IPeriod $that): bool
    {
        return $this->microseconds !== $that->microseconds()
            || $this->seconds !== $that->seconds()
            || $this->minutes !== $that->minutes()
            || $this->hours !== $that->hours()
            || $this->days !== $that->days()
            || $this->months !== $that->months()
            || $this->years !== $that->years();
    }

    public function isSimilar(IPeriod $that): bool
    {
        return 0 === $this->compareTo($that);
    }

    public function isNotSimilar(IPeriod $that): bool
    {
        return 0 !== $this->compareTo($that);
    }

    public function isGreater(IPeriod $that): bool
    {
        return (0 < $this->compareTo($that));
    }

    public function isGreaterOrEqual(IPeriod $that): bool
    {
        return (0 <= $this->compareTo($that));
    }

    public function isLess(IPeriod $that): bool
    {
        return (0 > $this->compareTo($that));
    }

    public function isLessOrEqual(IPeriod $that): bool
    {
        return (0 >= $this->compareTo($that));
    }

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

    public function isPositive(): bool
    {
        return $this->hasPositiveValues()
            && !$this->hasNegativeValues();
    }

    public function isNegative(): bool
    {
        return $this->hasNegativeValues()
            && !$this->hasPositiveValues();
    }

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
    ): static {
        if (is_int($years)) {
            $period = static::of(
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
    ): static {
        if (is_int($years)) {
            $period = static::of(
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
     * @param bool $changed
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
     * @param bool $changed
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

    public function with(
        ?int $years = null,
        ?int $months = null,
        ?int $days = null,
        ?int $hours = null,
        ?int $minutes = null,
        ?int $seconds = null,
        ?int $microseconds = null,
    ): static {
        return new static(
            $years ?? $this->years,
            $months ?? $this->months,
            $days ?? $this->days,
            $hours ?? $this->hours,
            $minutes ?? $this->minutes,
            $seconds ?? $this->seconds,
            $microseconds ?? $this->microseconds
        );
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
    ): Ok|Error {
        try {
            $period = $this->plus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
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
    ): Ok|Error {
        try {
            $period = $this->minus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($period);
    }

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
}
