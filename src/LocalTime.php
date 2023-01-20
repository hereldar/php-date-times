<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTime as MutableStandardDateTime;
use DateTimeImmutable as StandardDateTime;
use DateTimeInterface as StandardDateTimeInterface;
use DateTimeZone as StandardTimeZone;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;
use Throwable;
use UnexpectedValueException;

class LocalTime implements ILocalTime
{
    private readonly StandardDateTime $value;

    public function __construct(StandardDateTime $value)
    {
        $this->value = $value
            ->setDate(1970, 1, 1)
            ->setTimezone(new StandardTimeZone('UTC'));
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
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
    ): static {
        $string = sprintf(
            '%d:%02d:%02d.%06d',
            $hour,
            $minute,
            $second,
            $microsecond,
        );

        return static::parse($string, '!G:i:s.u');
    }

    public static function parse(
        string $string,
        string $format = ILocalTime::ISO8601,
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
        return static::parse($value, ILocalTime::ISO8601);
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalTime::RFC2822);
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        return static::parse($value, ($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339);
    }

    public static function fromStandardDateTime(StandardDateTimeInterface $value): static
    {
        if ($value instanceof MutableStandardDateTime) {
            $value = StandardDateTime::createFromMutable($value);
        } elseif (!$value instanceof StandardDateTime) {
            $value = StandardDateTime::createFromInterface($value);
        }

        return new static($value);
    }

    public function format(string $format = ILocalTime::ISO8601): string
    {
        return $this->value->format($format);
    }

    public function toIso8601(): string
    {
        return $this->format(ILocalTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->format(ILocalTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->format(($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339);
    }

    public function toStandardDateTime(): StandardDateTime
    {
        return $this->value;
    }

    public function atDate(ILocalDate $date): ILocalDateTime
    {
        $dt = $this->value->setDate(
            $date->year(),
            $date->month(),
            $date->day(),
        );

        return new LocalDateTime($dt);
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

    public function compareTo(ILocalTime $that): int
    {
        $a = $this->value;
        $b = $that->toStandardDateTime();

        return match (true) {
            ($a == $b) => 0,
            ($a > $b) => 1,
            default => -1,
        };
    }

    public function isEqual(ILocalTime $that): bool
    {
        return ($this->value == $that->toStandardDateTime());
    }

    public function isNotEqual(ILocalTime $that): bool
    {
        return ($this->value != $that->toStandardDateTime());
    }

    public function isGreater(ILocalTime $that): bool
    {
        return ($this->value > $that->toStandardDateTime());
    }

    public function isGreaterOrEqual(ILocalTime $that): bool
    {
        return ($this->value >= $that->toStandardDateTime());
    }

    public function isLess(ILocalTime $that): bool
    {
        return ($this->value < $that->toStandardDateTime());
    }

    public function isLessOrEqual(ILocalTime $that): bool
    {
        return ($this->value <= $that->toStandardDateTime());
    }

    public function plus(
        ?IPeriod $period = null,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        if ($period !== null) {
            if (func_num_args() !== 1) {
                throw new \Exception();
            }
        } else {
            $period = Period::of(
                hours: $hours,
                minutes: $minutes,
                seconds: $seconds,
                milliseconds: $milliseconds,
                microseconds: $microseconds
            )->toStandardDateInterval();
        }

        $value = $this->value->add($period);

        return new static($value);
    }

    public function minus(
        ?IPeriod $period = null,
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        if ($period !== null) {
            if (func_num_args() !== 1) {
                throw new \Exception();
            }
        } else {
            $period = Period::of(
                hours: $hours,
                minutes: $minutes,
                seconds: $seconds,
                milliseconds: $milliseconds,
                microseconds: $microseconds
            )->toStandardDateInterval();
        }

        $value = $this->value->sub($period);

        return new static($value);
    }

    public function with(
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        ?int $microsecond = null,
    ): static {
        return new static($this->value->setTime(
            $hour ?? $this->hour(),
            $minute ?? $this->minute(),
            $second ?? $this->second(),
            $microsecond ?? $this->microsecond(),
        ));
    }

    public function add(
        ?IPeriod $period = null,
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
