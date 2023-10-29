<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneGroup;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Exceptions\CountryException;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Stringable;
use Throwable;
use ValueError;

/**
 * A time-zone, such as `America/Mexico_City`.
 *
 * There are three different types of time-zone rules:
 *
 * -   Fixed offset from UTC/Greenwich (`-06:00`).
 * -   Time-zone identifiers as published in the IANA time-zone
 *     database (`Australia/Hobart`).
 * -   Time-zone abbreviations (`BST`).
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @psalm-consistent-constructor
 */
class TimeZone implements Stringable
{
    /** @var array<class-string, array<string, static>> */
    private static array $timeZones = [];

    /**
     * @internal
     */
    private function __construct(
        private readonly NativeTimeZone $value,
    ) {}

    /**
     * Outputs this time-zone as a `string`, using its name.
     */
    public function __toString(): string
    {
        return $this->name();
    }

    /**
     * The time-zone for UTC/Greenwich.
     */
    public static function utc(): static
    {
        return static::of('UTC');
    }

    /**
     * The system default time-zone.
     */
    public static function system(): static
    {
        return static::of(date_default_timezone_get());
    }

    /**
     * Gets the list of available time-zone identifiers.
     *
     * The list of identifiers may grow over time. Results can be
     * filtered by `TimeZoneGroup`.
     *
     * @return array<int, string>
     */
    public static function identifiers(
        TimeZoneGroup $group = TimeZoneGroup::All,
    ): array {
        return NativeTimeZone::listIdentifiers($group->value);
    }

    /**
     * Gets the list of time-zone identifiers for a single country.
     *
     * @param string $code a two-letter (uppercase) ISO 3166-1 country code
     *
     * @throws CountryException if the country cannot be found
     *
     * @return array<int, string>
     */
    public static function countryIdentifiers(
        string $code,
    ): array {
        try {
            $identifiers = NativeTimeZone::listIdentifiers(
                NativeTimeZone::PER_COUNTRY,
                $code,
            );
        } catch (ValueError) {
            throw new CountryException($code);
        }

        if (!$identifiers) {
            throw new CountryException($code);
        }

        return $identifiers;
    }

    /**
     * Makes a new `TimeZone` with the specified name.
     *
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

    /**
     * Makes a new `TimeZone` from a native `DateTimeZone`.
     */
    public static function fromNative(
        NativeTimeZone $value,
    ): static {
        $class = static::class;
        $name = $value->getName();

        return self::$timeZones[$class][$name] ??= new static($value);
    }

    /**
     * Makes a new `TimeZone` from a fixed `Offset`.
     */
    public static function fromOffset(
        Offset $offset,
    ): static {
        return static::of($offset->toIso8601(false));
    }

    /**
     * Returns a native `DateTimeZone` with the information of this
     * time-zone.
     */
    public function toNative(): NativeTimeZone
    {
        return $this->value;
    }

    /**
     * Returns the offset of this time-zone from UTC/Greenwich on the
     * specified date.
     */
    public function toOffset(
        LocalDate|LocalDateTime|null $date = null,
    ): Offset {
        $date ??= LocalDateTime::now();

        $seconds = $this->value->getOffset($date->toNative());

        return Offset::fromTotalSeconds($seconds);
    }

    /**
     * Returns the name of this time-zone.
     */
    public function name(): string
    {
        return $this->value->getName();
    }

    /**
     * Returns the type of this time-zone.
     */
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

    /**
     * Compares the name of this time-zone to the name of another
     * time-zone.
     *
     * Returns a negative integer, zero, or a positive integer
     * depending on whether the name of this time-zone is less than,
     * equal to, or greater than the name of the given time-zone name.
     */
    public function compareTo(TimeZone $that): int
    {
        $dt = DateTime::epoch()->toNative();

        $a = $this->value;
        $b = $that->toNative();

        $result = $a->getOffset($dt) <=> $b->getOffset($dt);

        if ($result !== 0) {
            return $result;
        }

        return ($a->getName() <=> $b->getName());
    }

    /**
     * Checks if the given time-zone belongs to the same class and has
     * the same name as this time-zone.
     */
    public function is(TimeZone $that): bool
    {
        return $this::class === $that::class
            && $this->name() === $that->name();
    }

    /**
     * Checks if the given time-zone belongs to another class or has a
     * different name than this time-zone.
     */
    public function isNot(TimeZone $that): bool
    {
        return $this::class !== $that::class
            || $this->name() !== $that->name();
    }

    /**
     * Checks if the given time-zone has the same name as this
     * time-zone.
     */
    public function isEqual(TimeZone $that): bool
    {
        return ($this->name() === $that->name());
    }

    /**
     * Checks if the given time-zone has a different name from this
     * time-zone.
     */
    public function isNotEqual(TimeZone $that): bool
    {
        return ($this->name() !== $that->name());
    }
}
