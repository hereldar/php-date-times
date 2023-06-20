
# Offset


A time offset from Greenwich/UTC, such as `-06:00`.

Although time offsets usually consist of a fixed number of hours
and minutes, this class also supports seconds.

Instances of this class are immutable and not affected by any
method calls.


## Constants


### ISO8601

```php
final public const ISO8601 = '%R%H:%I';
```


### RFC2822

```php
final public const RFC2822 = '%R%H%I';
```


### RFC3339

```php
final public const RFC3339 = '%R%H:%I';
```


### SQL

```php
final public const SQL = '%R%H:%I';
``` 


### HOURS_MAX

```php
public const HOURS_MAX = +15;
```


### HOURS_MIN

```php
public const HOURS_MIN = -15;
```


### MINUTES_MAX

```php
public const MINUTES_MAX = +59;
```


### MINUTES_MIN

```php
public const MINUTES_MIN = -59;
```


### SECONDS_MAX

```php
public const SECONDS_MAX = +59;
```


### SECONDS_MIN

```php
public const SECONDS_MIN = -59;
```


### TOTAL_MINUTES_MAX

```php
public const TOTAL_MINUTES_MAX = +900;
```


### TOTAL_MINUTES_MIN

```php
public const TOTAL_MINUTES_MIN = -900;
```


### TOTAL_SECONDS_MAX

```php
public const TOTAL_SECONDS_MAX = +54000;
```


### TOTAL_SECONDS_MIN

```php
public const TOTAL_SECONDS_MIN = -54000;
```


## Static Methods


### zero

```php
public static function zero(): static;
```

The offset for UTC (00:00:00).


### max

```php
public static function max(): static;
```

The maximum supported offset (15:00:00).


### min

```php
public static function min(): static;
```

The minimum supported offset (-15:00:00).


### of

```php
public static function of(
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
): static;
```

Makes a new `Offset` with the specified hours, minutes and
seconds. The time units must be within their valid range, and
the resulting offset must be in the range -15:00 to +15:00,
otherwise an exception will be thrown.

All parameters are optional and, if not specified, will take
their UTC value (00:00:00).

**Parameters:**

`$hours` the amount of hours, from -15 to 15

`$minutes` the amount of minutes, from -59 to 59

`$seconds` the amount of seconds, from -59 to 59

**Exceptions:**

`OutOfRangeException` if the value of any unit is out of range


### fromTotalMinutes

```php
public static function fromTotalMinutes(int $minutes): static;
```

Makes a new `Offset` with the specified total number of minutes.
The resulting offset must be in the range -15:00 to +15:00.

**Parameters:**

`$minutes` total number of minutes, from -900 to 900

**Exceptions:**

`OutOfRangeException` if the total is not in the required range


### fromTotalSeconds

```php
public static function fromTotalSeconds(int $seconds): static;
```

Makes a new `Offset` with the specified total number of seconds.
The resulting offset must be in the range -15:00 to +15:00.

**Parameters:**

`$seconds` total number of seconds, from -54,000 to 54,000

**Exceptions:**

`OutOfRangeException` if the total is not in the required range


### parse

```php
public static function parse(
    string $string,
    string|array $format = Offset::ISO8601,
): Ok|Error;
```

Makes a new `Offset` from a text string using a specific format.
It also accepts a list of formats.

If the format is not specified, the ISO 8601 offset format will
be used (`%R%H:%I`).

The `Offset` is not returned directly, but a [result][php-results-doc] that will
contain the time if no error was found, or an exception if
something went wrong.

**Parameters:**

`$string` the text to parse

`$format` the expected format, or a list of accepted formats

**Return Values:**

`Ok<static>` if no error is found

`Error<ParseException>` if the text cannot be parsed

`Error<OutOfRangeException>` if the value of any unit is out of range

**Exceptions:**

`InvalidArgumentException` if an empty list of formats is passed


### fromIso8601

```php
public static function fromIso8601(string $string): static;
```

Makes a new `Offset` from a text with the ISO 8601 offset
format (e.g. `'+02:30'`).

The offset is returned directly if no error is found, otherwise
an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed

`OutOfRangeException` if the value of any unit is out of range


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this offset as a `string`, using the default format of
the class.


### format

```php
public function format(string $format = Offset::ISO8601): Ok|Error;
```

Formats this offset using the specified format.

If the format is not specified, the ISO 8601 offset format will
be used (`%R%H:%I`).

The text is not returned directly, but a [result][php-results-doc]
that will contain the text if no error was found, or an exception if
something went wrong.

**Return Values:**

`Ok<string>` if no error is found

`Error<FormatException>` if the format is incorrect


### formatted

```php
public function formatted(string $format = Offset::ISO8601): string;
```

Formats this offset using the specified format.

If the format is not specified, the ISO 8601 offset format will
be used (`%R%H:%I`).

The text is returned directly if no error is found, otherwise
an exception is thrown.

**Exceptions:**

`FormatException` if the format is incorrect


### toIso8601

```php
public function toIso8601(?bool $seconds = null): string;
```

Formats this offset with the ISO 8601 offset format (e.g.
`'+02:30'`).

By default, adds seconds if they are non-zero (for example
`'+02:30:45'`). To always add them, set `$seconds` to true. To
never add them, set `$seconds` to false.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc2822

```php
public function toRfc2822(?bool $seconds = null): string;
```

Formats this offset with the RFC 2822 offset format (e.g.
`'+0230'`).

By default, adds seconds if they are non-zero (for example
`'+023045'`). To always add them, set `$seconds` to true. To
never add them, set `$seconds` to false.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc3339

```php
public function toRfc3339(?bool $seconds = null): string;
```

Formats this offset with the RFC 3339 offset format (e.g.
`'+02:30'`).

By default, adds seconds if they are non-zero (for example
`'+02:30:45'`). To always add them, set `$seconds` to true. To
never add them, set `$seconds` to false.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toSql

```php
public function toSql(?bool $seconds = null): string;
```

Formats this offset with the SQL offset format (e.g. `'+02:30'`).

By default, adds seconds if they are non-zero (for example
`'+02:30:45'`). To always add them, set `$seconds` to true. To
never add them, set `$seconds` to false.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toTimeZone

```php
public function toTimeZone(): TimeZone;
```

Returns a fixed `TimeZone` with this offset.


### hours

```php
public function hours(): int;
```

Returns the amount of hours, from -15 to 15.


### minutes

```php
public function minutes(): int;
```

Returns the amount of minutes, from -59 to 59.


### seconds

```php
public function seconds(): int;
```

Returns the amount of seconds, from -59 to 59.


### totalMinutes

```php
public function totalMinutes(): int;
```

Returns the total number of minutes, from -900 to 900.


### totalSeconds

```php
public function totalSeconds(): int;
```

Returns the total number of seconds, from -54,000 to 54,000.


### compareTo

```php
public function compareTo(Offset $that): int;
```

Compares this offset to another offset.

Returns a negative integer, zero, or a positive integer as this
offset is less than, equal to, or greater than the given offset.


### is

```php
public function is(Offset $that): bool;
```

Checks if the given offset belongs to the same class and has
the same value as this offset.


### isNot

```php
public function isNot(Offset $that): bool;
```

Checks if the given offset belongs to another class or has a
different value than this offset.


### isEqual

```php
public function isEqual(Offset $that): bool;
```

Checks if the given offset has the same value as this offset.


### isNotEqual

```php
public function isNotEqual(Offset $that): bool;
```

Checks if the given offset has a different value from this
offset.


### isGreater

```php
public function isGreater(Offset $that): bool;
```

Checks if this offset is greater than the specified offset.


### isGreaterOrEqual

```php
public function isGreaterOrEqual(Offset $that): bool;
```

Checks if this offset is greater than or equal to the specified
offset.


### isLess

```php
public function isLess(Offset $that): bool;
```

Checks if this offset is less than the specified offset.


### isLessOrEqual

```php
public function isLessOrEqual(Offset $that): bool;
```

Checks if this offset is less than or equal to the specified
offset.


### isPositive

```php
public function isPositive(): bool;
```

Checks if this offset is greater than zero.


### isNegative

```php
public function isNegative(): bool;
```

Checks if this offset is less than zero.


### isZero

```php
public function isZero(): bool;
```

Checks if this offset is equal to zero.


### plus

```php
public function plus(
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
): static;
```

Returns a copy of this offset with the specified amount of
hours, minutes and seconds added.

**Exceptions:**

`ArithmeticError` if any value exceeds the PHP limits for an integer

`OutOfRangeException` if the value of any unit is out of range


### minus

```php
public function minus(
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
): static;
```

Returns a copy of this offset with the specified amount of
hours, minutes and seconds subtracted.

**Exceptions:**

`ArithmeticError` if any value exceeds the PHP limits for an integer

`OutOfRangeException` if the value of any unit is out of range


### with

```php
public function with(
    ?int $hours = null,
    ?int $minutes = null,
    ?int $seconds = null,
): static;
```

Returns a copy of this offset with the specified hours, minutes
and seconds.

**Parameters:**

`$hours` the amount of hours, from -15 to 15

`$minutes` the amount of minutes, from -59 to 59

`$seconds` the amount of seconds, from -59 to 59

**Exceptions:**

`OutOfRangeException` if the value of any unit is out of range


### add

```php
public function add(
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
): Ok|Error;
```

Makes a copy of this offset with the specified amount of hours,
minutes and seconds added.

It works the same as the [plus()](#plus) method, but returns a
[result][php-results-doc] instead of the new offset.

The result will contain the new offset if no error was found,
or an exception if something went wrong.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

`Error<OutOfRangeException>` if the value of any unit is out of range


### subtract

```php
public function subtract(
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
): Ok|Error;
```

Makes a copy of this offset with the specified amount of hours,
minutes and seconds subtracted.

It works the same as the [minus()](#minus) method, but returns
a [result][php-results-doc] instead of the new offset.

The result will contain the new offset if no error was found,
or an exception if something went wrong.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

`Error<OutOfRangeException>` if the value of any unit is out of range


### copy

```php
public function copy(
    ?int $hour = null,
    ?int $minute = null,
    ?int $second = null,
    ?int $microsecond = null,
): Ok|Error;
```

Makes a copy of this offset with the specified hours, minutes
and seconds.

It works the same as the [with()](#with) method, but returns a
[result][php-results-doc] instead of the new offset.

The result will contain the new offset if no error was found, or
an exception if something went wrong.

**Parameters:**

`$hours` the amount of hours, from -15 to 15

`$minutes` the amount of minutes, from -59 to 59

`$seconds` the amount of seconds, from -59 to 59

**Return Values:**

`Ok<static>` if no error is found

`Error<OutOfRangeException>` if the value of any unit is out of range


[php-results-doc]: https://hereldar.github.io/php-results/
