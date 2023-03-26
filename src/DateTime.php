<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTime as MutableStandardDateTime;
use DateTimeImmutable as StandardDateTime;
use DateTimeInterface as StandardDateTimeInterface;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ILocalTime;
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
class DateTime implements IDateTime, Stringable
{
    private function __construct(
        private readonly StandardDateTime $value
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function now(
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): static {
        try {
            $tz = match (true) {
                $timeZone instanceof ITimeZone => $timeZone->toStandard(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toStandard(),
                is_string($timeZone) => TimeZone::of($timeZone)->toStandard(),
            };

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
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        int $microsecond = 0,
        ITimeZone|IOffset|string $timeZone = 'UTC',
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

        return static::parse($string, 'Y-n-j G:i:s.u', $timeZone)->orFail();
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string $format = IDateTime::ISO8601,
        ITimeZone|IOffset|string $timeZone = 'UTC',
    ): Ok|Error {
        $tz = match (true) {
            $timeZone instanceof ITimeZone => $timeZone->toStandard(),
            $timeZone instanceof IOffset => $timeZone->toTimeZone()->toStandard(),
            is_string($timeZone) => TimeZone::of($timeZone)->toStandard(),
        };

        $dt = StandardDateTime::createFromFormat($format, $string, $tz);

        if (false === $dt) {
            $info = StandardDateTime::getLastErrors();
            $firstError = ($info)
                ? (reset($info['errors']) ?: reset($info['warnings']) ?: null)
                : null;

            return Error::withException(new ParseException($string, $format, $firstError));
        }

        /** @var Ok<static> */
        return Ok::withValue(new static($dt));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, IDateTime::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, IDateTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        return static::parse($value, ($milliseconds)
            ? IDateTime::RFC3339_EXTENDED
            : IDateTime::RFC3339)->orFail();
    }

    public static function fromStandard(StandardDateTimeInterface $value): static
    {
        if ($value instanceof MutableStandardDateTime) {
            $value = StandardDateTime::createFromMutable($value);
        }

        return new static($value);
    }

    public function format(string $format = IDateTime::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(IDateTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(IDateTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->value->format(($milliseconds)
            ? IDateTime::RFC3339_EXTENDED
            : IDateTime::RFC3339);
    }

    public function toStandard(): StandardDateTime
    {
        return $this->value;
    }

    public function timestamp(): int
    {
        return $this->value->getTimestamp();
    }

    public function date(): ILocalDate
    {
        return LocalDate::fromStandard($this->value);
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
        return LocalTime::fromStandard($this->value);
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

    public function offset(): IOffset
    {
        return Offset::fromTotalSeconds(
            $this->value->getOffset()
        );
    }

    public function timezone(): ITimeZone
    {
        return TimeZone::fromStandard(
            $this->value->getTimezone()
        );
    }

    public function compareTo(IDateTime $that): int
    {
        return ($this->value <=> $that->toStandard());
    }

    public function is(IDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(IDateTime $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(IDateTime $that): bool
    {
        return ($this->value == $that->toStandard());
    }

    public function isNotEqual(IDateTime $that): bool
    {
        return ($this->value != $that->toStandard());
    }

    public function isGreater(IDateTime $that): bool
    {
        return ($this->value > $that->toStandard());
    }

    public function isGreaterOrEqual(IDateTime $that): bool
    {
        return ($this->value >= $that->toStandard());
    }

    public function isLess(IDateTime $that): bool
    {
        return ($this->value < $that->toStandard());
    }

    public function isLessOrEqual(IDateTime $that): bool
    {
        return ($this->value <= $that->toStandard());
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
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
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

        $value = (!$overflow && ($period->months() || $period->years()))
            ? Adder::addPeriodWithoutOverflow($this->value, $period)
            : $this->value->add($period->toStandard());

        return new static($value);
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
        bool $overflow = false,
    ): static {
        if (is_int($years)) {
            $period = Period::of(
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

        $value = (!$overflow && ($period->months() || $period->years()))
            ? Adder::addPeriodWithoutOverflow($this->value, $period->negated())
            : $this->value->sub($period->toStandard());

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
        ITimeZone|IOffset|string|null $timeZone = null,
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

        if ($timeZone !== null) {
            $dt = $dt->setTimezone(match (true) {
                $timeZone instanceof ITimeZone => $timeZone->toStandard(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toStandard(),
                is_string($timeZone) => TimeZone::of($timeZone)->toStandard(),
            });
        }

        return new static($dt);
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
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->plus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
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
        int $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
        bool $overflow = false,
    ): Ok|Error {
        try {
            $dateTime = $this->minus(
                $years, $months, $weeks, $days,
                $hours, $minutes, $seconds,
                $milliseconds, $microseconds,
                $overflow,
            );
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        /** @var Ok<static> */
        return Ok::withValue($dateTime);
    }
}
