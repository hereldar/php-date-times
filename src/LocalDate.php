<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

/**
 * @psalm-consistent-constructor
 */
class LocalDate implements ILocalDate, Stringable
{
    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function today(
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): static {
        try {
            $tz = match (true) {
                is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
                $timeZone instanceof ITimeZone => $timeZone->toNative(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toNative(),
            };

            $dt = new NativeDateTime('today', $tz);
        } catch (Throwable $e) {
            throw new UnexpectedValueException(
                message: get_debug_type($timeZone),
                previous: $e
            );
        }

        return new static($dt);
    }

    public static function of(
        int $year,
        int $month = 1,
        int $day = 1,
    ): static {
        $string = "{$year}-{$month}-{$day}";

        return static::parse($string, 'Y-n-j')->orFail();
    }

    /**
     * @param string|array<int, string> $format
     *
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string|array $format = ILocalDate::ISO8601,
    ): Ok|Error {
        $tz = TimeZone::utc()->toNative();

        /** @var array<int, string> $formats */
        $formats = [];

        if (!$format) {
            throw new InvalidArgumentException(
                'At least one format must be passed'
            );
        }

        if (is_array($format)) {
            $formats = $format;
            $format = reset($formats);
        }

        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        if (false !== $dt) {
            /** @var Ok<static> */
            return Ok::withValue(new static($dt));
        }

        $info = NativeDateTime::getLastErrors();

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
                if (!str_starts_with($fmt, '!')) {
                    $fmt = "!{$fmt}";
                }

                $dt = NativeDateTime::createFromFormat($fmt, $string, $tz);

                if (false !== $dt) {
                    /** @var Ok<static> */
                    return Ok::withValue(new static($dt));
                }
            }
        }

        $firstError = ($info)
            ? (reset($info['errors']) ?: reset($info['warnings']) ?: null)
            : null;

        return Error::withException(new ParseException($string, $format, $firstError));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, ILocalDate::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalDate::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value): static
    {
        return static::parse($value, ILocalDate::RFC3339)->orFail();
    }

    public static function fromSql(string $value): static
    {
        return static::parse($value, ILocalDate::SQL)->orFail();
    }

    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    public function format(string $format = ILocalDate::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(ILocalDate::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalDate::RFC2822);
    }

    public function toRfc3339(): string
    {
        return $this->value->format(ILocalDate::RFC3339);
    }

    public function toSql(): string
    {
        return $this->value->format(ILocalDate::SQL);
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function atTime(ILocalTime $time): ILocalDateTime
    {
        $dt = $this->value->setTime(
            $time->hour(),
            $time->minute(),
            $time->second(),
            $time->microsecond(),
        );

        return LocalDateTime::fromNative($dt);
    }

    public function year(): int
    {
        return (int) $this->value->format('Y');
    }

    public function month(): int
    {
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
        return (int) $this->value->format('j');
    }

    public function dayOfWeek(): int
    {
        return (int) $this->value->format('N');
    }

    public function dayOfYear(): int
    {
        return (int) $this->value->format('z') + 1;
    }

    public function compareTo(ILocalDate $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(ILocalDate $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(ILocalDate $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(ILocalDate $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(ILocalDate $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(ILocalDate $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(ILocalDate $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(ILocalDate $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(ILocalDate $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    public function plus(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of($years, $months, $weeks, $days);
        } elseif (!$months && !$weeks && !$days) {
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
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of($years, $months, $weeks, $days);
        } elseif (!$months && !$weeks && !$days) {
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
    ): static {
        return new static(
            $this->value->setDate(
                $year ?? $this->year(),
                $month ?? $this->month(),
                $day ?? $this->day(),
            )
        );
    }

    public function add(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->plus(
                $years, $months, $weeks, $days,
                $overflow,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }

    public function subtract(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->minus(
                $years, $months, $weeks, $days,
                $overflow,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
