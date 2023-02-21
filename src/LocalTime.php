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
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\ILocalTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

class LocalTime implements ILocalTime, Stringable
{
    private function __construct(
        private readonly StandardDateTime $value,
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

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    /**
     * @return IResult<static, ParseException>
     */
    public static function parse(
        string $string,
        string $format = ILocalTime::ISO8601,
    ): IResult {
        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $tz = new StandardTimeZone('UTC');

        $dt = StandardDateTime::createFromFormat($format, $string, $tz);

        if (false === $dt) {
            $info = StandardDateTime::getLastErrors();
            $firstError = ($info)
                ? (reset($info['errors']) ?: reset($info['warnings']) ?: null)
                : null;

            return Error::withException(new ParseException($string, $format, $firstError));
        }

        return Ok::withValue(new static($dt));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, ILocalTime::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, ILocalTime::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value, bool $milliseconds = false): static
    {
        return static::parse($value, ($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339)->orFail();
    }

    public static function fromStandard(
        StandardDateTimeInterface $value
    ): static {
        $string = $value->format('G:i:s.u');

        return static::parse($string, 'G:i:s.u')->orFail();
    }

    public function format(string $format = ILocalTime::ISO8601): IResult
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(ILocalTime::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(ILocalTime::RFC2822);
    }

    public function toRfc3339(bool $milliseconds = false): string
    {
        return $this->value->format(($milliseconds)
            ? ILocalTime::RFC3339_EXTENDED
            : ILocalTime::RFC3339);
    }

    public function toStandard(): StandardDateTime
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

        return LocalDateTime::fromStandard($dt);
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
        return ($this->value <=> $that->toStandard());
    }

    public function is(ILocalTime $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(ILocalTime $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(ILocalTime $that): bool
    {
        return ($this->value == $that->toStandard());
    }

    public function isNotEqual(ILocalTime $that): bool
    {
        return ($this->value != $that->toStandard());
    }

    public function isGreater(ILocalTime $that): bool
    {
        return ($this->value > $that->toStandard());
    }

    public function isGreaterOrEqual(ILocalTime $that): bool
    {
        return ($this->value >= $that->toStandard());
    }

    public function isLess(ILocalTime $that): bool
    {
        return ($this->value < $that->toStandard());
    }

    public function isLessOrEqual(ILocalTime $that): bool
    {
        return ($this->value <= $that->toStandard());
    }

    public function plus(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        $period = $this->createPeriod(func_get_args());

        $value = $this->value->add($period->toStandard());

        return new static($value);
    }

    public function minus(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): static {
        $period = $this->createPeriod(func_get_args());

        $value = $this->value->sub($period->toStandard());

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
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
    ): IResult {
        try {
            $dateTime = $this->plus(...func_get_args());
        } catch (ArithmeticError $e) {
            return Error::withException($e);
        }

        return Ok::withValue($dateTime);
    }

    public function subtract(
        int|IPeriod $hours = 0,
        int $minutes = 0,
        int $seconds = 0,
        int $milliseconds = 0,
        int $microseconds = 0,
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
        // Hours or Period
        if (isset($args[0]) && $args[0] instanceof IPeriod) {
            $period = $args[0];
            unset($args[0]);
        }

        if (!isset($period)) {
            $period = Period::of(0, 0, 0, 0, ...$args);
        } elseif ($args) {
            throw new InvalidArgumentException('No time units are allowed when a period is passed');
        }

        return $period;
    }
}
