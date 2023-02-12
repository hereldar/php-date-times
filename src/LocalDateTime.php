<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTime as MutableStandardDateTime;
use DateTimeImmutable as StandardDateTime;
use DateTimeInterface as StandardDateTimeInterface;
use DateTimeZone as StandardTimeZone;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Throwable;
use UnexpectedValueException;

class LocalDateTime implements ILocalDateTime
{
    private readonly StandardDateTime $value;

    public function __construct(StandardDateTime $value)
    {
        $this->value = $value->setTimezone(
            new StandardTimeZone('UTC')
        );
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public static function now(
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): static {
        try {
            $tz = (is_string($timeZone))
                ? new StandardTimeZone($timeZone)
                : $timeZone->toStandardTimeZone();

            $dt = new StandardDateTime('now', $tz);
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
        int $month,
        int $day,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): static {
        $string = sprintf(
            '%d-%d-%d %d:%02d:%02d.%06d',
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        return static::parse($string, '!Y-n-j G:i:s.u');
    }

    public static function parse(
        string $string,
        string $format = ILocalDateTime::ISO8601,
    ): static {
        $tz = new StandardTimeZone('UTC');

        $dt = StandardDateTime::createFromFormat($format, $string, $tz);

        if (false === $dt) {
            throw new UnexpectedValueException($string);
        }

        return new static($dt);
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, ILocalDateTime::ISO8601);
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalDateTime::RFC2822);
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        return static::parse($value, ($milliseconds)
            ? ILocalDateTime::RFC3339_EXTENDED
            : ILocalDateTime::RFC3339);
    }

    public static function fromStandard(StandardDateTimeInterface $value): static
    {
        if ($value instanceof MutableStandardDateTime) {
            $value = StandardDateTime::createFromMutable($value);
        } elseif (!$value instanceof StandardDateTime) {
            $value = StandardDateTime::createFromInterface($value);
        }

        return new static($value);
    }

    public function format(string $format = ILocalDateTime::ISO8601): string
    {
        return $this->value->format($format);
    }

    public function toIso8601(): string
    {
        return $this->format(ILocalDateTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->format(ILocalDateTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->format(($milliseconds)
            ? ILocalDateTime::RFC3339_EXTENDED
            : ILocalDateTime::RFC3339);
    }

    public function toStandard(): StandardDateTime
    {
        return $this->value;
    }

    public function atTimeZone(ITimeZone $timeZone): IDateTime
    {
        $dt = $this->value->setTimezone(
            $timeZone->toStandardTimeZone()
        );

        return new DateTime($dt);
    }

    public function atOffset(IOffset $offset): IDateTime
    {
        $dt = $this->value->setTimezone(
            $offset->toStandardTimeZone()
        );

        return new DateTime($dt);
    }

    public function date(): ILocalDate
    {
        return LocalDate::parse($this->format('Y-n-j'), 'Y-n-j');
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

    public function time(): ILocalTime
    {
        return LocalTime::parse($this->format('G:i:s.u'), 'G:i:s.u');
    }

    public function hour(): int
    {
        return (int) $this->value->format('G');
    }

    public function minute(): int
    {
        return (int) $this->value->format('i');
    }

    public function second(): int
    {
        return (int) $this->value->format('s');
    }

    public function millisecond(): int
    {
        return (int) $this->value->format('v');
    }

    public function microsecond(): int
    {
        return (int) $this->value->format('u');
    }

    public function compareTo(ILocalDateTime $that): int
    {
        $a = $this->value;
        $b = $that->toStandard();

        return match (true) {
            ($a == $b) => 0,
            ($a > $b) => 1,
            default => -1,
        };
    }

    public function isEqual(ILocalDateTime $that): bool
    {
        return ($this->value == $that->toStandard());
    }

    public function isNotEqual(ILocalDateTime $that): bool
    {
        return ($this->value != $that->toStandard());
    }

    public function isGreater(ILocalDateTime $that): bool
    {
        return ($this->value > $that->toStandard());
    }

    public function isGreaterOrEqual(ILocalDateTime $that): bool
    {
        return ($this->value >= $that->toStandard());
    }

    public function isLess(ILocalDateTime $that): bool
    {
        return ($this->value < $that->toStandard());
    }

    public function isLessOrEqual(ILocalDateTime $that): bool
    {
        return ($this->value <= $that->toStandard());
    }

    public function plus(
        ?IPeriod $period = null,
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
        if ($period !== null) {
            if (func_num_args() !== 1) {
                throw new InvalidArgumentException('No time units are allowed when a period is passed');
            }
        } else {
            $period = Period::of(
                years: $years,
                months: $months,
                weeks: $weeks,
                days: $days,
                hours: $hours,
                minutes: $minutes,
                seconds: $seconds,
                milliseconds: $milliseconds,
                microseconds: $microseconds
            )->toStandard();
        }

        $value = $this->value->add($period);

        return new static($value);
    }

    public function minus(
        ?IPeriod $period = null,
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
        if ($period !== null) {
            if (func_num_args() !== 1) {
                throw new InvalidArgumentException('No time units are allowed when a period is passed');
            }
        } else {
            $period = Period::of(
                years: $years,
                months: $months,
                weeks: $weeks,
                days: $days,
                hours: $hours,
                minutes: $minutes,
                seconds: $seconds,
                milliseconds: $milliseconds,
                microseconds: $microseconds
            )->toStandard();
        }

        $value = $this->value->sub($period);

        return new static($value);
    }

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
            $dt = $dt->setDate(
                $year ?? $this->year(),
                $month ?? $this->month(),
                $day ?? $this->day(),
            );
        }

        if ($hour !== null
            || $minute !== null
            || $second !== null
            || $microsecond !== null) {
            $dt = $dt->setTime(
                $hour ?? $this->hour(),
                $minute ?? $this->minute(),
                $second ?? $this->second(),
                $microsecond ?? $this->microsecond(),
            );
        }

        return new static($dt);
    }

    public function add(
        ?IPeriod $period = null,
        int $years = 0,
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
            $dateTime = $this->plus(...func_get_args());
        } catch (Throwable $e) {
            return Error::withException($e);
        }

        return Ok::withValue($dateTime);
    }

    public function subtract(
        ?IPeriod $period = null,
        int $years = 0,
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
            $dateTime = $this->minus(...func_get_args());
        } catch (Throwable $e) {
            return Error::withException($e);
        }

        return Ok::withValue($dateTime);
    }
}
