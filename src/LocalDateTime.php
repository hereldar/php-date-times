<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTime as MutableStandardDateTime;
use DateTimeImmutable as StandardDateTime;
use DateTimeInterface as StandardDateTimeInterface;
use DateTimeZone as StandardTimeZone;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\IPeriod;
use Hereldar\DateTimes\Interfaces\IDateTime;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

class LocalDateTime implements ILocalDateTime, Stringable
{
    protected readonly StandardDateTime $value;

    private function __construct(StandardDateTime $value)
    {
        $this->value = $value->setTimezone(
            new StandardTimeZone('UTC')
        );
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
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
        int $month = 1,
        int $day = 1,
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

        return static::parse($string, '!Y-n-j G:i:s.u')->orFail();
    }

    /**
     * @return IResult<static, ParseException>
     */
    public static function parse(
        string $string,
        string $format = ILocalDateTime::ISO8601,
    ): IResult {
        $tz = new StandardTimeZone('UTC');

        $dt = StandardDateTime::createFromFormat($format, $string, $tz);

        if (false === $dt) {
            return Error::withException(new ParseException($string, $format));
        }

        return Ok::withValue(new static($dt));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, ILocalDateTime::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalDateTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        return static::parse($value, ($milliseconds)
            ? ILocalDateTime::RFC3339_EXTENDED
            : ILocalDateTime::RFC3339)->orFail();
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

    public function format(string $format = ILocalDateTime::ISO8601): IResult
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(ILocalDateTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalDateTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->value->format(($milliseconds)
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
        return LocalDate::parse(
            $this->value->format('Y-n-j'),
            '!Y-n-j'
        )->orFail();
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
        return LocalTime::parse(
            $this->value->format('G:i:s.u'),
            '!G:i:s.u',
        )->orFail();
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
        return ($this->value <=> $that->toStandard());
    }

    public function is(ILocalDateTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(ILocalDateTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
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
        $period = $this->createPeriod(func_get_args());

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
        $period = $this->createPeriod(func_get_args());

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
    ): IResult {
        try {
            $dateTime = $this->plus(...func_get_args());
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

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
    ): IResult {
        try {
            $dateTime = $this->minus(...func_get_args());
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        return Ok::withValue($dateTime);
    }

    private function createPeriod(array $args): IPeriod
    {
        if (isset($args['years'])) {
            if ($args['years'] instanceof IPeriod) {
                $period = $args['years'];
                unset($args['years']);
            }
        } elseif (isset($args[0])) {
            if ($args[0] instanceof IPeriod) {
                $period = $args[0];
                unset($args[0]);
            }
        }

        if (isset($args['overflow'])) {
            unset($args['overflow']);
        } elseif (isset($args[9])) {
            unset($args[9]);
        }

        if (isset($period)) {
            if (array_filter($args)) {
                throw new InvalidArgumentException('No time units are allowed when a period is passed');
            }
        } else {
            $period = Period::of(...$args);
        }

        return $period;
    }
}
