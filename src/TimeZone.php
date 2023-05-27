<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\Interfaces\ILocalDate;
use Hereldar\DateTimes\Interfaces\ILocalDateTime;
use Hereldar\DateTimes\Interfaces\IOffset;
use Hereldar\DateTimes\Interfaces\ITimeZone;
use Stringable;
use Throwable;

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

        if (isset(self::$cache[$class][$name])) {
            return self::$cache[$class][$name];
        }

        try {
            $timeZone = new static(new NativeTimeZone($name));
        } catch (Throwable $e) {
            throw new TimeZoneException($name, $e);
        }

        self::$cache[$class][$name] = $timeZone;
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

    public static function fromOffset(
        IOffset $offset
    ): static {
        return static::of($offset->toIso8601(false));
    }

    public function toNative(): NativeTimeZone
    {
        return $this->value;
    }

    public function toOffset(ILocalDate|ILocalDateTime|null $date = null): IOffset
    {
        $date ??= LocalDateTime::now();

        $seconds = $this->value->getOffset($date->toNative());

        return Offset::fromTotalSeconds($seconds);
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

    public function compareTo(ITimeZone $that): int
    {
        $dt = DateTime::epoch()->toNative();

        $a = $this->value;
        $b = $that->toNative();

        $result = $a->getOffset($dt) <=> $b->getOffset($dt);

        if ($result !== 0) {
            return $result;
        }

        return $a->getName() <=> $b->getName();
    }

    public function is(ITimeZone $that): bool
    {
        return $this::class === $that::class
            && $this->name() === $that->name();
    }

    public function isNot(ITimeZone $that): bool
    {
        return $this::class !== $that::class
            || $this->name() !== $that->name();
    }

    public function isEqual(ITimeZone $that): bool
    {
        return ($this->name() === $that->name());
    }

    public function isNotEqual(ITimeZone $that): bool
    {
        return ($this->name() !== $that->name());
    }
}
