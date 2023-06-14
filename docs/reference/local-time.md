
# LocalTime

A time without a time-zone in the ISO-8601 calendar system, such as
17:30:09.

This class does not store a date or time-zone.  Instead, it is a
description of the local time as seen on a wall clock. It cannot
represent an instant on the time-line without additional
information such as an offset or time-zone.

Instances of this class are immutable and not affected by any
method calls.


## Constants


### ISO8601

```php
final public const ISO8601 = 'H:i:s';
```


### ISO8601_MILLISECONDS

```php
final public const ISO8601_MILLISECONDS = 'H:i:s.v';
```


### ISO8601_MICROSECONDS

```php
final public const ISO8601_MICROSECONDS = 'H:i:s.u';
```


### RFC2822

```php
final public const RFC2822 = 'H:i:s';
```


### RFC3339

```php
final public const RFC3339 = 'H:i:s';
```


### RFC3339_MILLISECONDS

```php
final public const RFC3339_MILLISECONDS = 'H:i:s.v';
```


### RFC3339_MICROSECONDS

```php
final public const RFC3339_MICROSECONDS = 'H:i:s.u';
```


### SQL

```php
final public const SQL = 'H:i:s';
```


### SQL_MILLISECONDS

```php
final public const SQL_MILLISECONDS = 'H:i:s.v';
```


### SQL_MICROSECONDS

```php
final public const SQL_MICROSECONDS = 'H:i:s.u';
```


## Static Methods


### max

```php
public static function max(): static;
```

The maximum supported time (23:59:59.999999).


### min

```php
public static function min(): static;
```

The minimum supported time (00:00:00).


### midnight

```php
public static function midnight(): static;
```

The time of midnight at the start of the day (00:00:00).


### noon

```php
public static function noon(): static;
```

The time of noon in the middle of the day (12:00:00).


### now

```php
public static function now(
    TimeZone|Offset|string $timeZone = 'UTC',
): static;
```

Obtains the current time from the system clock in the specified
time-zone. If no time-zone is specified, the `UTC` time-zone
will be used.

**Throws:**

`TimeZoneException` if the time-zone name cannot be found


### of

```php
public static function of(
    int $hour = 0,
    int $minute = 0,
    int $second = 0,
    int $microsecond = 0,
): static;
```

Makes a new `LocalTime` with the specified hour, minute, second
and microsecond. The time units must be within their valid
range, otherwise an exception will be thrown.

All parameters are optional and, if not specified, will take
their Unix epoch value (00:00:00).

**Parameters:**

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Throws:**

`OutOfRangeException` if the value of any unit is out of range


### parse

```php
public static function parse(
    string $string,
    string|array $format = LocalTime::ISO8601,
): Ok|Error;
```

Makes a new `LocalTime` from a text string using a specific
format. It also accepts a list of formats.

If the format is not specified, the ISO 8601 time format will
be used (`H:i:s`).

The `LocalTime` is not returned directly, but a [result][php-results-doc]
that will contain the time if no error was found, or an exception if
something went wrong.

**Parameters:**

`$string` the text to parse

`$format` the expected format, or a list of accepted formats

**Returns:**

`Ok<string>` if no error was found

`Error<ParseException>` if something went wrong

**Throws:**

`InvalidArgumentException` if an empty list of formats is passed


### fromIso8601

```php
public static function fromIso8601(
    string $value,
    bool $milliseconds = false,
    bool $microseconds = false,
): static;
```

Makes a new `LocalTime` from a text with the ISO 8601 time
format (e.g. `'17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
setting respectively `$milliseconds` or `$microseconds` to true.

The time is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromRfc2822

```php
public static function fromRfc2822(string $value): static;
```

Makes a new `LocalTime` from a text with the RFC 2822 time
format (e.g. `'17:30:09'`).

The time is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromRfc3339

```php
public static function fromRfc3339(
    string $value,
    bool $milliseconds = false,
    bool $microseconds = false,
): static;
```

Makes a new `LocalTime` from a text with the RFC 3339 time
format (e.g. `'17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
setting respectively `$milliseconds` or `$microseconds` to true.

The time is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromSql

```php
public static function fromSql(
    string $value,
    bool $milliseconds = false,
    bool $microseconds = false,
): static;
```

Makes a new `LocalTime` from a text with the SQL time format
(e.g. `'17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'17:30:09.105'`) or microseconds (e.g. `'17:30:09.382172'`) by
setting respectively `$milliseconds` or `$microseconds` to true.

The time is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromNative

```php
public static function fromNative(
    NativeDateTimeInterface $value
): static;
```

Makes a new `LocalTime` from a native `DateTime` or
`DateTimeImmutable`.

Only the time values will be taken, while date and time-zone
values will be ignored.


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this time as a `string`, using the default format of
the class.


### format

```php
public function format(string $format = LocalTime::ISO8601): Ok|Error;
```

Formats this time using the specified format.

If the format is not specified, the ISO 8601 time format will
be used (`H:i:s`).

The text is not returned directly, but a [result][php-results-doc]
that will contain the text if no error was found, or an exception if
something went wrong.

**Returns:**

`Ok<string>` if no error was found

`Error<FormatException>` if something went wrong


### toIso8601

```php
public function toIso8601(
    bool $milliseconds = false,
    bool $microseconds = false,
): string;
```

Formats this time with the ISO 8601 time format (e.g.
`'17:30:09'`).

It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
microseconds (e.g. `'17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc2822

```php
public function toRfc2822(): string;
```

Formats this time with the RFC 2822 time format (e.g.
`'17:30:09'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc3339

```php
public function toRfc3339(
    bool $milliseconds = false,
    bool $microseconds = false,
): string;
```

Formats this time with the RFC 3339 time format (e.g.
`'17:30:09'`).

It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
microseconds (e.g. `'17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toSql

```php
public function toSql(
    bool $milliseconds = false,
    bool $microseconds = false,
): string;
```

Formats this time with the SQL time format (e.g. `'17:30:09'`).

It is possible to add milliseconds (e.g. `'17:30:09.105'`) or
microseconds (e.g. `'17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toNative

```php
public function toNative(): NativeDateTime;
```

Returns a native `DateTimeImmutable` with the values of this
time.

The date and time-zone values will be taken from the Unix epoch
(1 January 1970 UTC).


### atDate

```php
public function atDate(
    LocalDate|int $year = 1970,
    int $month = 1,
    int $day = 1,
): LocalDateTime;
```

Combines this time with a date to make a `LocalDateTime`. It
accepts a `LocalDate` or individual time units.

If a `LocalDate` is passed as the first argument, no further
arguments will be accepted.

If individual time units are passed, they must be within their
valid range. Missing units will be taken from the Unix epoch
(1 January 1970).

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

**Throws:**

`InvalidArgumentException` if a `LocalDate` is combined with some time units

`OutOfRangeException` if the value of any unit is out of range


### hour

```php
public function hour(): int;
```

Returns the hour of the day, from 0 to 23.


### minute

```php
public function minute(): int;
```

Returns the minute of the hour, from 0 to 59.


### second

```php
public function second(): int;
```

Returns the second of the minute, from 0 to 59.


### millisecond

```php
public function millisecond(): int;
```

Returns the millisecond of the second, from 0 to 999.


### microsecond

```php
public function microsecond(): int;
```

Returns the microsecond of the second, from 0 to 999,999.


### compareTo

```php
public function compareTo(LocalTime $that): int;
```

Compares this time to another time.

Returns a negative integer, zero, or a positive integer as this
time is before, equal to, or after the given time.


### is

```php
public function is(LocalTime $that): bool;
```

Checks if the given time belongs to the same class and has the
same value as this time.


### isNot

```php
public function isNot(LocalTime $that): bool;
```

Checks if the given time belongs to another class or has a
different value than this time.


### isEqual

```php
public function isEqual(LocalTime $that): bool;
```

Checks if the given time has the same value as this time.


### isNotEqual

```php
public function isNotEqual(LocalTime $that): bool;
```

Checks if the given time has a different value from this time.


### isGreater

```php
public function isGreater(LocalTime $that): bool;
```

Checks if this time is after the specified time.


### isGreaterOrEqual

```php
public function isGreaterOrEqual(LocalTime $that): bool;
```

Checks if this time is after or equal to the specified time.


### isLess

```php
public function isLess(LocalTime $that): bool;
```

Checks if this time is before the specified time.


### isLessOrEqual

```php
public function isLessOrEqual(LocalTime $that): bool;
```

Checks if this time is before or equal to the specified time.


### plus

```php
public function plus(
    int|Period $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
    int $microseconds = 0,
    int $milliseconds = 0,
): static;
```

Returns a copy of this time with the specified amount of hours,
minutes, seconds and microseconds added. It accepts a `Period`
or individual time units.

If a `Period` is passed as the first argument, no individual
time unit must be specified.

WARNING: It is strongly recommended to use named arguments to
specify units other than hours, minutes, seconds and
microseconds, since only the order of the four first parameters
is guaranteed.

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### minus

```php
public function minus(
    int|Period $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
    int $microseconds = 0,
    int $milliseconds = 0,
): static;
```

Returns a copy of this time with the specified amount of hours,
minutes, seconds and microseconds subtracted. It accepts a
`Period` or individual time units.

If a `Period` is passed as the first argument, no individual
time unit must be specified.

WARNING: It is strongly recommended to use named arguments to
specify units other than hours, minutes, seconds and
microseconds, since only the order of the four first parameters
is guaranteed.

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### with

```php
public function with(
    ?int $hour = null,
    ?int $minute = null,
    ?int $second = null,
    ?int $microsecond = null,
): static;
```

Returns a copy of this time with the specified hour, minute,
second and microsecond.

**Parameters:**

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Throws:**

`OutOfRangeException` if the value of any unit is out of range


### add

```php
public function add(
    int|Period $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
    int $microseconds = 0,
    int $milliseconds = 0,
): Ok;
```

Makes a copy of this time with the specified amount of hours,
minutes, seconds and microseconds added. It works the same as
the [plus()](#plus) method, but returns a [result][php-results-doc]
instead of the new time.

The result will contain the new time if no error was found, or
an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Returns:**

`Ok<static>` if no error was found

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### subtract

```php
public function subtract(
    int|Period $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
    int $microseconds = 0,
    int $milliseconds = 0,
): Ok;
```

Makes a copy of this time with the specified amount of hours,
minutes, seconds and microseconds subtracted. It works the same
as the [minus()](#minus) method, but returns a [result][php-results-doc]
instead of the new time.

The result will contain the new time if no error was found, or
an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Returns:**

`Ok<static>` if no error was found

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### copy

```php
public function copy(
    ?int $hour = null,
    ?int $minute = null,
    ?int $second = null,
    ?int $microsecond = null,
): Ok|Error;
```

Makes a copy of this time with the specified hour, minute,
second and microsecond. It works the same as the [with()](#with)
method, but returns a [result][php-results-doc] instead of the new
time.

The result will contain the new time if no error was found, or
an exception if something went wrong.

**Parameters:**

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Returns:**

`Ok<static>` if no error was found

`Error<OutOfRangeException>` if something went wrong


[php-results-doc]: https://hereldar.github.io/php-results/
