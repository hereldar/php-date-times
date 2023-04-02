<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Stringable;

/**
 * @psalm-consistent-constructor
 */
class TimeZone implements ITimeZone, Stringable
{
    /** @var array<class-string, array<string, static>> */
    private static array $cache = [];

    private function __construct(
        private readonly NativeTimeZone $value,
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
        $class = static::class;

        $timeZone = (
            self::$cache[$class][$name]
                ??= new static(new NativeTimeZone($name))
        );

        self::$cache[$class][$timeZone->name()] = $timeZone;

        return $timeZone;
    }

    public static function fromNative(
        NativeTimeZone $value
    ): static {
        $class = static::class;
        $name = $value->getName();

        return self::$cache[$class][$name] ??= new static($value);
    }

    public function toNative(): NativeTimeZone
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
        $seconds = $this->value->getOffset($date->toNative());

        return Offset::fromTotalSeconds($seconds);
    }

    public function compareTo(ITimeZone $that): int
    {
        return ($this->value <=> $that->toNative());
    }

    public function is(ITimeZone $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class === $that::class
            && $this->value == $that->value; // @phpstan-ignore-line
    }

    public function isNot(ITimeZone $that): bool
    {
        /** @psalm-suppress NoInterfaceProperties */
        return $this::class !== $that::class
            || $this->value != $that->value; // @phpstan-ignore-line
    }

    public function isEqual(ITimeZone $that): bool
    {
        return ($this->value == $that->toNative());
    }

    public function isNotEqual(ITimeZone $that): bool
    {
        return ($this->value != $that->toNative());
    }
}
