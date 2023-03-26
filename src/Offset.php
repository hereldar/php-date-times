<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use OutOfRangeException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class Offset implements IOffset, Stringable
{
    private const HOURS_LIMIT = 18;
    private const MINUTES_LIMIT = 59;
    private const SECONDS_LIMIT = 59;
    private const TOTAL_MINUTES_LIMIT = (self::HOURS_LIMIT * 60) + self::MINUTES_LIMIT;
    private const TOTAL_SECONDS_LIMIT = (self::HOURS_LIMIT * 3600) + (self::MINUTES_LIMIT * 60) + self::SECONDS_LIMIT;

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

    private const FORMAT_PATTERN = '/%([%a-zA-Z])/';

    private function __construct(
        private readonly int $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function of(
        int $hours,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        if ($hours < -self::HOURS_LIMIT
            || $hours > +self::HOURS_LIMIT) {
            throw new OutOfRangeException();
        }

        if ($minutes < -self::MINUTES_LIMIT
            || $minutes > +self::MINUTES_LIMIT) {
            throw new OutOfRangeException();
        }

        if ($seconds < -self::SECONDS_LIMIT
            || $seconds > +self::SECONDS_LIMIT) {
            throw new OutOfRangeException();
        }

        return new static(($hours * 3600) + ($minutes * 60) + $seconds);
    }

    public static function fromTotalMinutes(int $minutes): static
    {
        if ($minutes < -self::TOTAL_MINUTES_LIMIT
            || $minutes > +self::TOTAL_MINUTES_LIMIT) {
            throw new OutOfRangeException();
        }

        return new static($minutes * 60);
    }

    public static function fromTotalSeconds(int $seconds): static
    {
        if ($seconds < -self::TOTAL_SECONDS_LIMIT
            || $seconds > +self::TOTAL_SECONDS_LIMIT) {
            throw new OutOfRangeException();
        }

        return new static($seconds);
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
        string $format = IOffset::ISO8601,
    ): Ok|Error {
        if ($format === IOffset::ISO8601) {
            /** @var Ok<static> */
            return Ok::withValue(static::fromIso8601($string));
        }

        $pattern = preg_replace_callback(
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

        /** @var Ok<static> */
        return Ok::withValue(static::of(
            hours: $sign * (int) ($matches['hours'] ?? 0),
            minutes: $sign * (int) ($matches['minutes'] ?? 0),
            seconds: $sign * (int) ($matches['seconds'] ?? 0),
        ));
    }

    public static function fromIso8601(string $string): static
    {
        $matches = [];

        if (!preg_match(self::ISO8601_PATTERN, $string, $matches)) {
            throw new ParseException($string, IOffset::ISO8601);
        }

        $sign = match ($matches['sign']) {
            '-' => -1,
            default => 1,
        };

        return static::of(
            hours: $sign * (int) ($matches['hours']),
            minutes: $sign * (int) ($matches['minutes']),
            seconds: $sign * (int) ($matches['seconds'] ?? 0),
        );
    }

    public function format(string $format = IOffset::ISO8601): Ok|Error
    {
        if ($format === IOffset::ISO8601) {
            return Ok::withValue($this->toIso8601());
        }

        $string = preg_replace_callback(
            pattern: self::FORMAT_PATTERN,
            callback: fn (array $matches) => match ($matches[1]) {
                '%' => '%',
                'R' => ($this->isNegative()) ? '-' : '+',
                'r' => ($this->isNegative()) ? '-' : '',
                'H' => sprintf('%02d', abs($this->hours())),
                'h' => (string) abs($this->hours()),
                'I' => sprintf('%02d', abs($this->minutes())),
                'i' => (string) abs($this->minutes()),
                'S' => sprintf('%02d', abs($this->seconds())),
                's' => (string) abs($this->seconds()),
                default => $matches[0],
            },
            subject: $format
        );

        if (!is_string($string)) {
            return Error::withException(new FormatException($format));
        }

        return Ok::withValue($string);
    }

    public function toIso8601(?bool $seconds = null): string
    {
        $string = sprintf(
            '%s%02d:%02d',
            ($this->value < 0) ? '-' : '+',
            abs($this->hours()),
            abs($this->minutes())
        );

        if ($seconds === true
            || ($seconds === null && $this->seconds())) {
            $string .= sprintf(
                ':%02d',
                abs($this->seconds())
            );
        }

        return $string;
    }

    public function toTimeZone(): ITimeZone
    {
        return TimeZone::of($this->toIso8601(false));
    }

    public function hours(): int
    {
        return intdiv($this->value, 3600);
    }

    public function minutes(): int
    {
        return $this->totalMinutes() % 60;
    }

    public function seconds(): int
    {
        return $this->value % 60;
    }

    public function totalMinutes(): int
    {
        return intdiv($this->value, 60);
    }

    public function totalSeconds(): int
    {
        return $this->value;
    }

    public function compareTo(IOffset $that): int
    {
        return $this->value <=> $that->totalSeconds();
    }

    public function is(IOffset $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value === $that->value; // @phpstan-ignore-line
    }

    public function isNot(IOffset $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value !== $that->value; // @phpstan-ignore-line
    }

    public function isEqual(IOffset $that): bool
    {
        return ($this->value === $that->totalSeconds());
    }

    public function isNotEqual(IOffset $that): bool
    {
        return ($this->value !== $that->totalSeconds());
    }

    public function isGreater(IOffset $that): bool
    {
        return ($this->value > $that->totalSeconds());
    }

    public function isGreaterOrEqual(IOffset $that): bool
    {
        return ($this->value >= $that->totalSeconds());
    }

    public function isLess(IOffset $that): bool
    {
        return ($this->value < $that->totalSeconds());
    }

    public function isLessOrEqual(IOffset $that): bool
    {
        return ($this->value <= $that->totalSeconds());
    }

    public function isNegative(): bool
    {
        return ($this->value < 0);
    }

    public function isPositive(): bool
    {
        return ($this->value > 0);
    }

    public function isZero(): bool
    {
        return ($this->value === 0);
    }

    public function plus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        return static::fromTotalSeconds(intadd($this->value, $totalSeconds));
    }

    public function minus(
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
    ): static {
        $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        return static::fromTotalSeconds(intsub($this->value, $totalSeconds));
    }

    public function abs(): static
    {
        return new static(abs($this->value));
    }

    public function negated(): static
    {
        return new static(-$this->value);
    }

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
}
