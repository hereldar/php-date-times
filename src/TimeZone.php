<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as StandardTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Stringable;

class TimeZone implements ITimeZone, Stringable
{
    private function __construct(
        private readonly StandardTimeZone $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->name();
    }

    public static function utc(): static
    {
        return static::of('UTC');
    }

    public static function system(): static
    {
        return static::of(date_default_timezone_get());
    }

    public static function of(
        string $name,
    ): static {
        return new static(new StandardTimeZone($name));
    }

    public static function fromStandard(
        StandardTimeZone $value
    ): static {
        return new static($value);
    }

    public function toStandard(): StandardTimeZone
    {
        return $this->value;
    }

    public function name(): string
    {
        return $this->value->getName();
    }

    public function type(): int
    {
        $name = $this->name();

        if (str_contains($name, '/')) {
            return TimeZoneType::TIMEZONE_IDENTIFIER;
        }

        if (str_starts_with($name, '+') || str_starts_with($name, '-')) {
            return TimeZoneType::UTC_OFFSET;
        }

        return TimeZoneType::TIMEZONE_ABBREVIATION;
    }

    public function offset(ILocalDate|ILocalDateTime $date): IOffset
    {
        $seconds = $this->value->getOffset($date->toStandard());

        return Offset::fromTotalSeconds($seconds);
    }

    public function compareTo(ITimeZone $that): int
    {
        return ($this->value <=> $that->toStandard());
    }

    public function is(ITimeZone $that): bool
    {
        return $this::class === $that::class
            && $this->value == $that->value;
    }

    public function isNot(ITimeZone $that): bool
    {
        return $this::class !== $that::class
            || $this->value != $that->value;
    }

    public function isEqual(ITimeZone $that): bool
    {
        return ($this->value == $that->toStandard());
    }

    public function isNotEqual(ITimeZone $that): bool
    {
        return ($this->value != $that->toStandard());
    }
}
