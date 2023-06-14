
# TimeZone

A time-zone, such as `Europe/Paris`.

There are three different types of time-zone rules:

-   Fixed offset from UTC/Greenwich (`-06:00`).
-   Time-zone identifiers as published in the IANA time-zone
    database (`Australia/Hobart`).
-   Time-zone abbreviations (`BST`).

Instances of this class are immutable and not affected by any
method calls.


## Static Methods


### utc

```php
public static function utc(): static;
```

The time-zone for UTC/Greenwich.


### system

```php
public static function system(): static;
```

The system default time-zone.


### identifiers

```php
public static function identifiers(
    TimeZoneGroup $group = TimeZoneGroup::All,
): array;
```

Gets the list of available time-zone identifiers.

The list of identifiers may grow over time. Results can be
filtered by `TimeZoneGroup`.

**Returns:**

`list<string>` the list of time-zone identifiers


### countryIdentifiers

```php
public static function countryIdentifiers(
    string $code,
): array;
```

Gets the list of time-zone identifiers for a single country.

**Parameters:**

`$code` a two-letter (uppercase) ISO 3166-1 country code

**Returns:**

`list<string>` the list of time-zone identifiers

**Throws:**

`CountryException` if the country cannot be found


### of

```php
public static function of(
    string $name,
): static;
```

Makes a new `TimeZone` with the specified name.

**Throws:**

`TimeZoneException` if the time-zone name cannot be found


### fromNative

```php
public static function fromNative(
    NativeTimeZone $value,
): static;
```

Makes a new `TimeZone` from a native `DateTimeZone`.


### fromOffset

```php
public static function fromOffset(
    Offset $offset,
): static;
```

Makes a new `TimeZone` from a fixed `Offset`.


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this time-zone as a `string`, using its name.


### toNative

```php
public function toNative(): NativeTimeZone;
```

Returns a native `DateTimeZone` with the information of this
time-zone.


### toOffset

```php
public function toOffset(
    LocalDate|LocalDateTime|null $date = null,
): Offset;
```

Returns the offset of this time-zone from UTC/Greenwich on the
specified date.


### name

```php
public function name(): string;
```

Returns the name of this time-zone.


### type

```php
public function type(): TimeZoneType;
```

Returns the type of this time-zone.


### compareTo

```php
public function compareTo(TimeZone $that): int;
```

Compares the name of this time-zone to the name of another
time-zone.

Returns a negative integer, zero, or a positive integer
depending on whether the name of this time-zone is less than,
equal to, or greater than the name of the given time-zone name.


### is

```php
public function is(TimeZone $that): bool;
```

Checks if the given time-zone belongs to the same class and has
the same name as this time-zone.


### isNot

```php
public function isNot(TimeZone $that): bool;
```

Checks if the given time-zone belongs to another class or has a
different name than this time-zone.


### isEqual

```php
public function isEqual(TimeZone $that): bool;
```

Checks if the given time-zone has the same name as this
time-zone.


### isNotEqual

```php
public function isNotEqual(TimeZone $that): bool;
```

Checks if the given time-zone has a different name from this
time-zone.
