<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Stringable;
use Throwable;

/**
 * @psalm-consistent-constructor
 */
class TimeZone implements Stringable
{
    /** @var array<class-string, array<string, static>> */
    private static array $timeZones = [];

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

    /**
     * @throws TimeZoneException if the time-zone name cannot be found
     */
    public static function of(
        string $name,
    ): static {
        $class = static::class;

        if (isset(self::$timeZones[$class][$name])) {
            return self::$timeZones[$class][$name];
        }

        try {
            $timeZone = new static(new NativeTimeZone($name));
        } catch (Throwable $e) {
            throw new TimeZoneException($name, $e);
        }

        self::$timeZones[$class][$name] = $timeZone;
        self::$timeZones[$class][$timeZone->name()] = $timeZone;

        return $timeZone;
    }

    public static function fromNative(
        NativeTimeZone $value
    ): static {
        $class = static::class;
        $name = $value->getName();

        return self::$timeZones[$class][$name] ??= new static($value);
    }

    public static function fromOffset(
        Offset $offset
    ): static {
        return static::of($offset->toIso8601(false));
    }

    public function toNative(): NativeTimeZone
    {
        return $this->value;
    }

    public function toOffset(LocalDate|LocalDateTime|null $date = null): Offset
    {
        $date ??= LocalDateTime::now();

        $seconds = $this->value->getOffset($date->toNative());

        return Offset::fromTotalSeconds($seconds);
    }

    public function name(): string
    {
        return $this->value->getName();
    }

    public function type(): TimeZoneType
    {
        $name = $this->name();

        if (str_contains($name, '/')) {
            return TimeZoneType::Identifier;
        }

        if (str_starts_with($name, '+') || str_starts_with($name, '-')) {
            return TimeZoneType::Offset;
        }

        return TimeZoneType::Abbreviation;
    }

    public function compareTo(TimeZone $that): int
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

    public function is(TimeZone $that): bool
    {
        return $this::class === $that::class
            && $this->name() === $that->name();
    }

    public function isNot(TimeZone $that): bool
    {
        return $this::class !== $that::class
            || $this->name() !== $that->name();
    }

    public function isEqual(TimeZone $that): bool
    {
        return ($this->name() === $that->name());
    }

    public function isNotEqual(TimeZone $that): bool
    {
        return ($this->name() !== $that->name());
    }
}
