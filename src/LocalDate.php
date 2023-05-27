<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use ArithmeticError;
use DateTimeImmutable as NativeDateTime;
use DateTimeInterface as NativeDateTimeInterface;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\DateTimes\Interfaces\Datelike;
use Hereldar\DateTimes\Interfaces\Formattable;
use Hereldar\DateTimes\Interfaces\Summable;
use Hereldar\DateTimes\Interfaces\Parsable;
use Hereldar\DateTimes\Services\Adder;
use Hereldar\DateTimes\Services\Validator;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use InvalidArgumentException;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class LocalDate implements Datelike, Formattable, Summable, Parsable, Stringable
{
    final public const ISO8601 = 'Y-m-d';
    final public const RFC2822 = 'D, d M Y';
    final public const RFC3339 = 'Y-m-d';
    final public const SQL = 'Y-m-d';

    private function __construct(
        private readonly NativeDateTime $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->format()->orFail();
    }

    public static function today(
        TimeZone|Offset|string $timeZone = 'UTC',
    ): static {
        $tz = match (true) {
            is_string($timeZone) => TimeZone::of($timeZone)->toNative(),
            $timeZone instanceof TimeZone => $timeZone->toNative(),
            $timeZone instanceof Offset => $timeZone->toTimeZone()->toNative(),
        };

        $dt = new NativeDateTime('today', $tz);

        if ($timeZone === 'UTC' || $tz->getName() === 'UTC') {
            return new static($dt);
        }

        $string = $dt->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    public static function of(
        int $year = 1970,
        int $month = 1,
        int $day = 1,
    ): static {
        Validator::month($month);
        Validator::day($day, $month, $year);

        if ($year < 0) {
            $extraYears = $year;
            $year = 0;
        } elseif ($year > 9999) {
            $extraYears = $year - 9999;
            $year = 9999;
        } else {
            $extraYears = 0;
        }

        $string = sprintf(
            '%04d-%d-%d',
            $year,
            $month,
            $day,
        );

        $dateTime = static::parse($string, 'Y-n-j')->orFail();

        if ($extraYears !== 0) {
            return $dateTime->plus($extraYears);
        }

        return $dateTime;
    }

    public static function parse(
        string $string,
        string|array $format = LocalDate::ISO8601,
    ): Ok|Error {
        $tz = TimeZone::utc()->toNative();

        /** @var array<int, string> $formats */
        $formats = [];

        if (is_array($format)) {
            if (count($format) === 0) {
                throw new InvalidArgumentException(
                    'At least one format must be passed'
                );
            }
            $formats = $format;
            $format = reset($formats);
        }

        $result = self::parseSimple($string, $format, $tz);

        if ($result->isOk()) {
            return $result;
        }

        if (count($formats) > 1) {
            while ($fmt = next($formats)) {
                $r = self::parseSimple($string, $fmt, $tz);

                if ($r->isOk()) {
                    return $r;
                }
            }
        }

        return $result;
    }

    /**
     * @return Ok<static>|Error<ParseException>
     */
    private static function parseSimple(
        string $string,
        string $format,
        NativeTimeZone $tz,
    ): Ok|Error {
        if (!str_starts_with($format, '!')) {
            $format = "!{$format}";
        }

        $dt = NativeDateTime::createFromFormat($format, $string, $tz);

        $info = NativeDateTime::getLastErrors();

        /** @psalm-suppress PossiblyFalseArgument */
        if (empty($info['errors']) && empty($info['warnings'])) {
            /** @var Ok<static> */
            return Ok::withValue(new static($dt));
        }

        /** @psalm-suppress PossiblyInvalidArrayAccess */
        $firstError = reset($info['errors']) ?: reset($info['warnings']) ?: null;

        return Error::withException(new ParseException($string, $format, $firstError));
    }

    public static function fromIso8601(string $value): static
    {
        return static::parse($value, self::ISO8601)->orFail();
    }

    public static function fromRfc2822(string $value): static
    {
        return static::parse($value, self::RFC2822)->orFail();
    }

    public static function fromRfc3339(string $value): static
    {
        return static::parse($value, self::RFC3339)->orFail();
    }

    public static function fromSql(string $value): static
    {
        return static::parse($value, self::SQL)->orFail();
    }

    public static function fromNative(
        NativeDateTimeInterface $value
    ): static {
        $string = $value->format('Y-n-j');

        return static::parse($string, 'Y-n-j')->orFail();
    }

    public function format(string $format = LocalDate::ISO8601): Ok|Error
    {
        return Ok::withValue($this->value->format($format));
    }

    public function toIso8601(): string
    {
        return $this->value->format(self::ISO8601);
    }

    public function toRfc2822(): string
    {
        return $this->value->format(self::RFC2822);
    }

    public function toRfc3339(): string
    {
        return $this->value->format(self::RFC3339);
    }

    public function toSql(): string
    {
        return $this->value->format(self::SQL);
    }

    public function toNative(): NativeDateTime
    {
        return $this->value;
    }

    public function atTime(LocalTime $time): LocalDateTime
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
        /** @var int<1, 12> */
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
        /** @var int<1, 31> */
        return (int) $this->value->format('j');
    }

    public function dayOfWeek(): int
    {
        /** @var int<1, 7> */
        return (int) $this->value->format('N');
    }

    public function dayOfYear(): int
    {
        /** @var int<1, 366> */
        return (int) $this->value->format('z') + 1;
    }

    public function inLeapYear(): bool
    {
        return ($this->value->format('L') === '1');
    }

    public function compareTo(LocalDate $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(LocalDate $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(LocalDate $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(LocalDate $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(LocalDate $that): bool
    {
        return ($this->value != $that->toNative());
    }

    public function isGreater(LocalDate $that): bool
    {
        return ($this->value > $that->toNative());
    }

    public function isGreaterOrEqual(LocalDate $that): bool
    {
        return ($this->value >= $that->toNative());
    }

    public function isLess(LocalDate $that): bool
    {
        return ($this->value < $that->toNative());
    }

    public function isLessOrEqual(LocalDate $that): bool
    {
        return ($this->value <= $that->toNative());
    }

    public function plus(
        int|Period $years = 0,
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
        int|Period $years = 0,
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
        int|Period $years = 0,
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
        int|Period $years = 0,
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
