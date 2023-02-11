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
use InvalidArgumentException;
use Throwable;
use UnexpectedValueException;

class LocalDate implements ILocalDate
{
    private readonly StandardDateTime $value;

    public function __construct(StandardDateTime $value)
    {
        $this->value = $value
            ->setTime(0, 0, 0, 0)
            ->setTimezone(new StandardTimeZone('UTC'));
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public static function today(
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
    ): static {
        $string = sprintf(
            '%d-%d-%d',
            $year,
            $month,
            $day,
        );

        return static::parse($string, '!Y-n-j');
    }

    public static function parse(
        string $string,
        string $format = ILocalDate::ISO8601,
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
        return static::parse($value, ILocalDate::ISO8601);
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalDate::RFC2822);
    }

    public static function fromRfc3339(string $value): static
    {
        return static::parse($value, ILocalDate::RFC3339);
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

    public function format(string $format = ILocalDate::ISO8601): string
    {
        return $this->value->format($format);
    }

    public function toIso8601(): string
    {
        return $this->format(ILocalDate::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->format(ILocalDate::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->format(($milliseconds)
            ? ILocalDate::RFC3339_EXTENDED
            : ILocalDate::RFC3339);
    }

    public function toStandardDateTime(): StandardDateTime
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

        return new LocalDateTime($dt);
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
        $a = $this->value;
        $b = $that->toStandardDateTime();

        return match (true) {
            ($a == $b) => 0,
            ($a > $b) => 1,
            default => -1,
        };
    }

    public function isEqual(ILocalDate $that): bool
    {
        return ($this->value == $that->toStandardDateTime());
    }

    public function isNotEqual(ILocalDate $that): bool
    {
        return ($this->value != $that->toStandardDateTime());
    }

    public function isGreater(ILocalDate $that): bool
    {
        return ($this->value > $that->toStandardDateTime());
    }

    public function isGreaterOrEqual(ILocalDate $that): bool
    {
        return ($this->value >= $that->toStandardDateTime());
    }

    public function isLess(ILocalDate $that): bool
    {
        return ($this->value < $that->toStandardDateTime());
    }

    public function isLessOrEqual(ILocalDate $that): bool
    {
        return ($this->value <= $that->toStandardDateTime());
    }

    public function plus(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
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
            )->toStandardDateInterval();
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
            )->toStandardDateInterval();
        }

        $value = $this->value->sub($period);

        return new static($value);
    }

    public function with(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
    ): static {
        return new static($this->value->setDate(
            $year ?? $this->year(),
            $month ?? $this->month(),
            $day ?? $this->day(),
        ));
    }

    public function add(
        ?IPeriod $period = null,
        int $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
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
    ): IResult {
        try {
            $dateTime = $this->minus(...func_get_args());
        } catch (Throwable $e) {
            return Error::withException($e);
        }

        return Ok::withValue($dateTime);
    }
}
