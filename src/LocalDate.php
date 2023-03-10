<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
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
use Hereldar\DateTimes\Services\Adder;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IResult;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;
use Throwable;
use UnexpectedValueException;

class LocalDate implements ILocalDate, Stringable
{
    private function __construct(
        private readonly StandardDateTime $value,
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
                $timeZone instanceof ITimeZone => $timeZone->toStandard(),
                $timeZone instanceof IOffset => $timeZone->toTimeZone()->toStandard(),
                is_string($timeZone) => TimeZone::of($timeZone)->toStandard(),
            };

            $dt = new StandardDateTime('today', $tz);
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
     * @return IResult<static, ParseException>
     */
    public static function parse(
        string $string,
        string $format = ILocalDate::ISO8601,
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

    public static function fromStandard(
        StandardDateTimeInterface $value
    ): static {
        $string = $value->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    public function format(string $format = ILocalDate::ISO8601): IResult
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

    public function toStandard(): StandardDateTime
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

        return LocalDateTime::fromStandard($dt);
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
        return ($this->value <=> $that->toStandard());
    }

    public function is(ILocalDate $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(ILocalDate $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(ILocalDate $that): bool
    {
        return ($this->value == $that->toStandard());
    }

    public function isNotEqual(ILocalDate $that): bool
    {
        return ($this->value != $that->toStandard());
    }

    public function isGreater(ILocalDate $that): bool
    {
        return ($this->value > $that->toStandard());
    }

    public function isGreaterOrEqual(ILocalDate $that): bool
    {
        return ($this->value >= $that->toStandard());
    }

    public function isLess(ILocalDate $that): bool
    {
        return ($this->value < $that->toStandard());
    }

    public function isLessOrEqual(ILocalDate $that): bool
    {
        return ($this->value <= $that->toStandard());
    }

    public function plus(
        int|IPeriod $years = 0,
        int $months = 0,
        int $weeks = 0,
        int $days = 0,
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
        // Years or Period
        if (isset($args[0]) && $args[0] instanceof IPeriod) {
            $period = $args[0];
            unset($args[0]);
        }

        // Overflow
        if (isset($args[4])) {
            unset($args[4]);
        }

        if (!isset($period)) {
            $period = Period::of(...$args);
        } elseif (array_filter($args)) {
            throw new InvalidArgumentException('No time units are allowed when a period is passed');
        }

        return $period;
    }
}
