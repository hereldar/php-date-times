<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateInterval as StandardDateInterval;
use Hereldar\DateTimes\Exceptions\Overflow;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;

class Period implements IPeriod
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

    private const FORMAT_PATTERN = '/%[%YyMmDdHhIiSsFf]/';

    public function __construct(
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
        return $this->format();
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

    public static function parse(
        string $string,
        string $format = IPeriod::ISO8601,
    ): static {
        if ($format === IPeriod::ISO8601) {
            return static::fromIso8601($string);
        }

        return new static();
    }

    public static function fromIso8601(string $value): static
    {
        $matches = [];

        if (!preg_match(self::ISO8601_PATTERN, $value, $matches)) {
            throw new \UnexpectedValueException();
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

    public static function fromStandardDateInterval(
        StandardDateInterval $interval,
    ): static {
        $sign = ($interval->invert) ? -1 : 1;

        return new static(
            $sign * $interval->y,
            $sign * $interval->m,
            $sign * $interval->d,
            $sign * $interval->h,
            $sign * $interval->i,
            $sign * $interval->s,
            $sign * ((int) round($interval->f * 1_000_000)),
        );
    }

    public function format(string $format = IPeriod::ISO8601): string
    {
        if ($format === IPeriod::ISO8601) {
            return $this->toIso8601();
        }

        return preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: static fn (array $matches) => match ($matches[0]) {
                '%' => '%',
                'Y' => sprintf('%02d', $this->years),
                'y' => $this->years,
                'M' => sprintf('%02d', $this->months),
                'm' => $this->months,
                'D' => sprintf('%02d', $this->days),
                'd' => $this->days,
                'H' => sprintf('%02d', $this->hours),
                'h' => $this->hours,
                'I' => sprintf('%02d', $this->minutes),
                'i' => $this->minutes,
                'S' => sprintf('%02d', $this->seconds),
                's' => $this->seconds,
                'F', 'U' => sprintf('%06d', $this->microseconds),
                'f' => rtrim(sprintf('%06d', $this->microseconds), '0'),
                'u' => $this->microseconds,
                default => $matches[0],
            },
            subject: $format
        );
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

    public function toStandardDateInterval(): StandardDateInterval
    {
        $interval = new StandardDateInterval('PT0S');

        $sign = ($this->isNegative()) ? -1 : 1;

        $interval->y = $sign * $this->years;
        $interval->m = $sign * $this->months;
        $interval->d = $sign * $this->days;
        $interval->h = $sign * $this->hours;
        $interval->i = $sign * $this->minutes;
        $interval->s = $sign * $this->seconds;
        $interval->f = (float) ($sign * $this->microseconds) / 1_000_000;

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
        return $this->microseconds === $that->microseconds()
            && $this->seconds === $that->seconds()
            && $this->minutes === $that->minutes()
            && $this->hours === $that->hours()
            && $this->days === $that->days()
            && $this->months === $that->months()
            && $this->years === $that->years();
    }

    public function isNot(IPeriod $that): bool
    {
        return $this->microseconds !== $that->microseconds()
            || $this->seconds !== $that->seconds()
            || $this->minutes !== $that->minutes()
            || $this->hours !== $that->hours()
            || $this->days !== $that->days()
            || $this->months !== $that->months()
            || $this->years !== $that->years();
    }

    public function isEqual(IPeriod $that): bool
    {
        return (0 === $this->compareTo($that));
    }

    public function isNotEqual(IPeriod $that): bool
    {
        return (0 !== $this->compareTo($that));
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

    public function isNegative(): bool
    {
        return $this->hasNegativeValues()
            && !$this->hasPositiveValues();
    }

    public function isPositive(): bool
    {
        return $this->hasPositiveValues()
            && !$this->hasNegativeValues();
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

    public function abs(): static
    {
        return new static(
            abs($this->years),
            abs($this->months),
            abs($this->days),
            abs($this->hours),
            abs($this->minutes),
            abs($this->seconds),
            abs($this->microseconds)
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
            $period = static::of(...func_get_args());
        } else {
            if (func_num_args() !== 1) {
                throw new \Exception();
            }
            $period = ($years instanceof StandardDateInterval)
                ? static::fromStandardDateInterval($years)
                : $years;
        }

        return new static(
            $this->years - $period->years(),
            $this->months - $period->months(),
            $this->days - $period->days(),
            $this->hours - $period->hours(),
            $this->minutes - $period->minutes(),
            $this->seconds - $period->seconds(),
            $this->microseconds - $period->microseconds(),
        );
    }

    public function multipliedBy(int $multiplicand): static
    {
        return static::of(
            years: $this->years * $multiplicand,
            months: $this->months * $multiplicand,
            days: $this->days * $multiplicand,
            hours: $this->hours * $multiplicand,
            minutes: $this->minutes * $multiplicand,
            seconds: $this->seconds * $multiplicand,
            microseconds: $this->microseconds * $multiplicand
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
            -$this->microseconds
        );
    }

    public function normalized(): static
    {
        $changed = false;

        $y = $this->years;
        $m = $this->months;
        $d = $this->days;
        $h = $this->hours;
        $i = $this->minutes;
        $s = $this->seconds;
        $f = $this->microseconds;

        $reversedValues = [&$f, &$s, &$i, &$h, &$d, &$m, &$y];
        $reversedFactors = [1_000_000, 60, 60, 24, 30, 12, 0];
        $this->normalizeOverflowedValues($reversedValues, $reversedFactors, $changed);

        if ($this->hasNegativeValues()
            && $this->hasPositiveValues()) {
            $values = [&$y, &$m, &$d, &$h, &$i, &$s, &$f];
            $factors = [12, 30, 24, 60, 60, 1_000_000, 0];
            $this->normalizeMixedSigns($values, $factors, $changed);

            $this->normalizeOverflowedValues($reversedValues, $reversedFactors, $changed);
        }

        if (!$changed) {
            return $this;
        }

        return new static($y, $m, $d, $h, $i, $s, $f);
    }

    private function normalizeOverflowedValues(array $values, array $factors, bool &$changed): void
    {
        $previousValue = 0;
        $previousFactor = 0;

        foreach ($values as $key => &$value) {
            $factor = $factors[$key];

            if ($previousFactor) {
                if ($overflow = intdiv($previousValue, $previousFactor)) {
                    $previousValue -= $overflow * $previousFactor;
                    $value += $overflow;
                    $changed = true;
                }
            }

            $previousValue = &$value;
            $previousFactor = $factor;
        }
    }

    private function normalizeMixedSigns(array $values, array $factors, bool &$changed): void
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
                        $previousValue -= 1;
                        $value += $previousFactor;
                        $changed = true;
                    }
                } elseif ($sign === -1) {
                    if ($value > 0) {
                        $previousValue += 1;
                        $value -= $previousFactor;
                        $changed = true;
                    }
                }
            }

            $previousValue = &$value;
            $previousFactor = $factor;
        }
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
            $period = static::of(...func_get_args());
        } else {
            if (func_num_args() !== 1) {
                throw new \Exception();
            }
            $period = ($years instanceof StandardDateInterval)
                ? static::fromStandardDateInterval($years)
                : $years;
        }

        return new static(
            $this->years + $period->years(),
            $this->months + $period->months(),
            $this->days + $period->days(),
            $this->hours + $period->hours(),
            $this->minutes + $period->minutes(),
            $this->seconds + $period->seconds(),
            $this->microseconds + $period->microseconds(),
        );
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
    ): IResult {
        try {
            $period = $this->plus(...func_get_args());
        } catch (Overflow $e) {
            return Error::withException($e);
        }

        return Ok::withValue($period);
    }

    public function divideBy(int $divisor): IResult
    {
        try {
            $period = $this->dividedBy($divisor);
        } catch (Overflow $e) {
            return Error::withException($e);
        }

        return Ok::withValue($period);
    }

    public function multiplyBy(int $multiplicand): IResult
    {
        try {
            $period = $this->multipliedBy($multiplicand);
        } catch (Overflow $e) {
            return Error::withException($e);
        }

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
    ): IResult {
        try {
            $period = $this->minus(...func_get_args());
        } catch (Overflow $e) {
            return Error::withException($e);
        }

        return Ok::withValue($period);
    }
}
