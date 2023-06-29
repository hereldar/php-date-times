
# LocalDateTime

A date and time without a time-zone in the ISO-8601 calendar system,
such as 17:30:09 on 3 December 2007.

This class does not store a time-zone. Instead, it is a description
of the date, as used for birthdays, combined with the local time as
seen on a wall clock. It cannot represent an instant on the
time-line without additional information such as an offset or
time-zone.

Instances of this class are immutable and not affected by any
method calls.


## Constants


### ISO8601

```php
final public const ISO8601 = 'Y-m-d\TH:i:s';
```


### ISO8601_MILLISECONDS

```php
final public const ISO8601_MILLISECONDS = 'Y-m-d\TH:i:s.v';
```


### ISO8601_MICROSECONDS

```php
final public const ISO8601_MICROSECONDS = 'Y-m-d\TH:i:s.u';
```


### RFC2822

```php
final public const RFC2822 = 'D, d M Y H:i:s';
```


### RFC3339

```php
final public const RFC3339 = 'Y-m-d\TH:i:s';
```


### RFC3339_MILLISECONDS

```php
final public const RFC3339_MILLISECONDS = 'Y-m-d\TH:i:s.v';
```


### RFC3339_MICROSECONDS

```php
final public const RFC3339_MICROSECONDS = 'Y-m-d\TH:i:s.u';
```


### SQL

```php
final public const SQL = 'Y-m-d H:i:s';
```


### SQL_MILLISECONDS

```php
final public const SQL_MILLISECONDS = 'Y-m-d H:i:s.v';
```


### SQL_MICROSECONDS

```php
final public const SQL_MICROSECONDS = 'Y-m-d H:i:s.u';
```


## Static Methods


### epoch

```php
public static function epoch(): static;
```

The Unix epoch (00:00:00 on 1 January 1970).


### now

```php
public static function now(
   TimeZone|Offset|string $timeZone = 'UTC',
): static;
```

Obtains the current date-time from the system clock in the
specified time-zone. If no time-zone is specified, the `UTC`
time-zone will be used.

**Exceptions:**

`TimeZoneException` if the time-zone name cannot be found


### of

```php
public static function of(
   int $year = 1970,
   int $month = 1,
   int $day = 1,
   int $hour = 0,
   int $minute = 0,
   int $second = 0,
   int $microsecond = 0,
): static;
```

Makes a new `LocalDateTime` with the specified year, month, day,
hour, minute, second and microsecond. The time units must be
within their valid range, otherwise an exception will be thrown.

All parameters are optional and, if not specified, will take
their Unix epoch value (00:00:00 on 1 January 1970).

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Exceptions:**

`OutOfRangeException` if the value of any unit is out of range


### parse

```php
public static function parse(
   string $string,
   string|array $format = LocalDateTime::ISO8601,
): Ok|Error;
```

Makes a new `LocalDateTime` from a text string using a specific
format. It also accepts a list of formats.

If the format is not specified, the ISO 8601 date-time format
will be used (`Y-m-d\TH:i:s`).

The `LocalDateTime` is not returned directly, but a [result][php-results-doc]
that will contain the date-time if no error was found, or an
exception if something went wrong.

**Parameters:**

`$string` the text to parse

`$format` the expected format, or a list of accepted formats

**Return Values:**

`Ok<string>` if no error is found

`Error<ParseException>` if the text cannot be parsed

**Exceptions:**

`InvalidArgumentException` if an empty list of formats is passed


### fromIso8601

```php
public static function fromIso8601(
   string $value,
   bool $milliseconds = false,
   bool $microseconds = false,
): static;
```

Makes a new `LocalDateTime` from a text with the ISO 8601
date-time format (e.g. `'2023-02-17T17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'2023-02-17T17:30:09.105'`) or microseconds (e.g.
`'2023-02-17T17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The date-time is returned directly if no error is found,
otherwise an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed


### fromRfc2822

```php
public static function fromRfc2822(string $value): static;
```

Makes a new `LocalDateTime` from a text with the RFC 2822
date-time format (e.g. `'Fri, 17 Feb 2023 17:30:09'`).

The date-time is returned directly if no error is found,
otherwise an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed


### fromRfc3339

```php
public static function fromRfc3339(
   string $value,
   bool $milliseconds = false,
   bool $microseconds = false,
): static;
```

Makes a new `LocalDateTime` from a text with the RFC 3339
date-time format (e.g. `'2023-02-17T17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'2023-02-17T17:30:09.105'`) or microseconds (e.g.
`'2023-02-17T17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The date-time is returned directly if no error is found,
otherwise an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed


### fromSql

```php
public static function fromSql(
   string $value,
   bool $milliseconds = false,
   bool $microseconds = false,
): static;
```

Makes a new `LocalDateTime` from a text with the SQL date-time
format (e.g. `'2023-02-17 17:30:09'`).

It is possible to parse texts with milliseconds (e.g.
`'2023-02-17 17:30:09.105'`) or microseconds (e.g.
`'2023-02-17 17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The date-time is returned directly if no error is found,
otherwise an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed


### fromNative

```php
public static function fromNative(NativeDateTimeInterface $value): static;
```

Makes a new `LocalDateTime` from a native [`DateTime`][native-date-time] or
[`DateTimeImmutable`][native-date-time-immutable].

Only the date and time values will be taken, while time-zone
values will be ignored.


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this date-time as a `string`, using the default format
of the class.


### format

```php
public function format(string $format = LocalDateTime::ISO8601): Ok|Error;
```

Formats this date-time using the specified format.

If the format is not specified, the ISO 8601 date-time format
will be used (`Y-m-d\TH:i:s`).

The text is not returned directly, but a [result][php-results-doc]
that will contain the text if no error was found, or an exception if
something went wrong.

**Return Values:**

`Ok<string>` if no error is found

`Error<FormatException>` if the format is incorrect


### formatted

```php
public function formatted(string $format = LocalDateTime::ISO8601): string;
```

Formats this date-time using the specified format.

If the format is not specified, the ISO 8601 date-time format
will be used (`Y-m-d\TH:i:s`).

The text is returned directly if no error is found, otherwise
an exception is thrown.

**Exceptions:**

`FormatException` if the format is incorrect


### toIso8601

```php
public function toIso8601(
   bool $milliseconds = false,
   bool $microseconds = false,
): string;
```

Formats this date-time with the ISO 8601 date-time format (e.g.
`'2023-02-17T17:30:09'`).

It is possible to add milliseconds (e.g.
`'2023-02-17T17:30:09.105'`) or microseconds (e.g.
`'2023-02-17T17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc2822

```php
public function toRfc2822(): string;
```

Formats this date-time with the RFC 2822 date-time format (e.g.
`'Fri, 17 Feb 2023 17:30:09'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc3339

```php
public function toRfc3339(
   bool $milliseconds = false,
   bool $microseconds = false,
): string;
```

Formats this date-time with the RFC 3339 date-time format (e.g.
`'2023-02-17T17:30:09'`).

It is possible to add milliseconds (e.g.
`'2023-02-17T17:30:09.105'`) or microseconds (e.g.
`'2023-02-17T17:30:09.382172'`) by setting respectively
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

Formats this date-time with the SQL date-time format (e.g.
`'2023-02-17 17:30:09'`).

It is possible to add milliseconds (e.g.
`'2023-02-17 17:30:09.105'`) or microseconds (e.g.
`'2023-02-17 17:30:09.382172'`) by setting respectively
`$milliseconds` or `$microseconds` to true.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toNative

```php
public function toNative(): NativeDateTime;
```

Returns a native [`DateTimeImmutable`][native-date-time-immutable] with the values of this
date-time.

The time-zone value will be taken from the Unix epoch (UTC).


### atTimeZone

```php
public function atTimeZone(TimeZone|string $timeZone): DateTime;
```

Combines this date-time with a time-zone to make a [`DateTime`](date-time).
It accepts a [`TimeZone`](time-zone) object or a text with its name.


### atOffset

```php
public function atOffset(
   Offset|int $hours = 0,
   int $minutes = 0,
   int $seconds = 0,
): DateTime;
```

Combines this date-time with an offset to make a [`DateTime`](date-time). It
accepts an `Offset` or individual time units.

If an `Offset` is passed as the first argument, no further
arguments will be accepted.

If individual time units are passed, they must be within their
valid range. Missing units will be zero (00:00:00).

**Parameters:**

`$hours` the amount of hours, from -15 to 15

`$minutes` the amount of minutes, from -59 to 59

`$seconds` the amount of seconds, from -59 to 59

**Exceptions:**

`InvalidArgumentException` if an `Offset` is combined with some time units

`OutOfRangeException` if the value of any unit is out of range


### date

```php
public function date(): LocalDate;
```

Returns a [`LocalDate`](local-date) with the same year,
month and day as this date-time.


### year

```php
public function year(): int;
```

Returns the year.


### month

```php
public function month(): int;
```

Returns the month as an `int` from 1 to 12.


### week

```php
public function week(): int;
```

Returns the ISO 8601 week number of year (weeks starting on
Monday).


### weekYear

```php
public function weekYear(): int;
```

Returns the ISO 8601 week-numbering year. This has the same
value as the normal year, except that if the ISO week number
belongs to the previous or next year, that year is used
instead.


### day

```php
public function day(): int;
```

Returns the day of the month, from 1 to 31.


### dayOfWeek

```php
public function dayOfWeek(): int;
```

Returns the day of the week as an `int` from 1 to 7.


### dayOfYear

```php
public function dayOfYear(): int;
```

Returns the day of the year as an `int` from 1 to 366.


### inLeapYear

```php
public function inLeapYear(): bool;
```

Returns whether it is a leap year.


### time

```php
public function time(): LocalTime;
```

Returns a [`LocalTime`](local-time) with the same hour,
minute, second and microsecond as this date-time.


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
public function compareTo(LocalDateTime $that): int;
```

Compares this date-time to another date-time.

Returns a negative integer, zero, or a positive integer as this
date-time is before, equal to, or after the given date-time.


### is

```php
public function is(LocalDateTime $that): bool;
```

Checks if the given date-time belongs to the same class and has
the same value as this date-time.


### isNot

```php
public function isNot(LocalDateTime $that): bool;
```

Checks if the given date-time belongs to another class or has a
different value than this date-time.


### isEqual

```php
public function isEqual(LocalDateTime $that): bool;
```

Checks if the given date-time has the same value as this
date-time.


### isNotEqual

```php
public function isNotEqual(LocalDateTime $that): bool;
```

Checks if the given date-time has a different value from this
date-time.


### isGreater

```php
public function isGreater(LocalDateTime $that): bool;
```

Checks if this date-time is after the specified date-time.


### isGreaterOrEqual

```php
public function isGreaterOrEqual(LocalDateTime $that): bool;
```

Checks if this date-time is after or equal to the specified
date-time.


### isLess

```php
public function isLess(LocalDateTime $that): bool;
```

Checks if this date-time is before the specified date-time.


### isLessOrEqual

```php
public function isLessOrEqual(LocalDateTime $that): bool;
```

Checks if this date-time is before or equal to the specified
date-time.


### plus

```php
public function plus(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   int $hours = 0,
   int $minutes = 0,
   int $seconds = 0,
   int $microseconds = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
   int $milliseconds = 0,
): static;
```

Returns a copy of this date-time with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
added. It accepts a `Period` or individual time units.

If a `Period` is passed as the first argument, no individual
time unit must be specified.

In some cases, adding the amount may make the resulting date
invalid. For example, adding a month to 31 January would result
in 31 February. In cases like this, the previous valid date
will be returned, which would be the last valid day of February
in this example.

This behaviour can be changed by setting `$overflow` to true.
If so, the overflow amount will be added to the following month,
which would result in 3 March or 2 March in this example.

WARNING: It is strongly recommended to use named arguments to
specify overflow and units other than years, months, days,
hours, minutes, seconds and microseconds, since only the order
of the seven first parameters is guaranteed.

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units

`ArithmeticError` if any value exceeds the PHP limits for an integer


### minus

```php
public function minus(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   int $hours = 0,
   int $minutes = 0,
   int $seconds = 0,
   int $microseconds = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
   int $milliseconds = 0,
): static;
```

Returns a copy of this date-time with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
subtracted. It accepts a `Period` or individual time units.

If a `Period` is passed as the first argument, no individual
time unit must be specified.

In some cases, subtracting the amount may make the resulting
date invalid. For example, subtracting a year from 29 February
2008 would result in 29 February 2007 (standard year). In cases
like this, the last valid day of the month will be returned,
which would be 28 February 2007 in this example.

This behaviour can be changed by setting `$overflow` to true.
If so, the overflow amount will be added to the following month,
which would result in 1 March in this example.

WARNING: It is strongly recommended to use named arguments to
specify overflow and units other than years, months, days,
hours, minutes, seconds and microseconds, since only the order
of the seven first parameters is guaranteed.

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units

`ArithmeticError` if any value exceeds the PHP limits for an integer


### with

```php
public function with(
   ?int $year = null,
   ?int $month = null,
   ?int $day = null,
   ?int $hour = null,
   ?int $minute = null,
   ?int $second = null,
   ?int $microsecond = null,
): static;
```

Returns a copy of this date-time with the specified year, month,
day, hour, minute, second and microsecond.

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Exceptions:**

`OutOfRangeException` if the value of any unit is out of range


### add

```php
public function add(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   int $hours = 0,
   int $minutes = 0,
   int $seconds = 0,
   int $microseconds = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
   int $milliseconds = 0,
): Ok|Error;
```

Makes a copy of this date-time with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
added.

It works the same as the [plus()](#plus) method, but returns a
[result][php-results-doc] instead of the new date-time.

The result will contain the new date-time if no error was found,
or an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units


### subtract

```php
public function subtract(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   int $hours = 0,
   int $minutes = 0,
   int $seconds = 0,
   int $microseconds = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
   int $milliseconds = 0,
): Ok|Error;
```

Makes a copy of this date-time with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
subtracted.

It works the same as the [minus()](#minus) method, but returns
a [result][php-results-doc] instead of the new date-time.

The result will contain the new date-time if no error was found,
or an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units


### copy

```php
public function copy(
   ?int $year = null,
   ?int $month = null,
   ?int $day = null,
   ?int $hour = null,
   ?int $minute = null,
   ?int $second = null,
   ?int $microsecond = null,
): Ok|Error;
```

Makes a copy of this date-time with the specified year, month, day,
hour, minute, second and microsecond.

It works the same as the [with()](#with) method, but returns a
[result][php-results-doc] instead of the new date-time.

The result will contain the new date-time if no error was found,
or an exception if something went wrong.

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

`$hour` the hour of the day, from 0 to 23

`$minute` the minute of the hour, from 0 to 59

`$second` the second of the minute, from 0 to 59

`$microsecond` the microsecond of the second, from 0 to 999,999

**Return Values:**

`Ok<static>` if no error is found

`Error<OutOfRangeException>` if the value of any unit is out of range


[native-date-time]: https://www.php.net/manual/en/class.datetime.php
[native-date-time-immutable]: https://www.php.net/manual/en/class.datetimeimmutable.php
[native-date-time-zone]: https://www.php.net/manual/en/class.datetimezone.php
[native-date-interval]: https://www.php.net/manual/en/class.dateinterval.php
[php-results-doc]: https://hereldar.github.io/php-results/
