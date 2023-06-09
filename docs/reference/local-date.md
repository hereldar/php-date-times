
# LocalDate

A date without a time-zone in the ISO-8601 calendar system, such as
3 December 2007.

This class does not store a time or time-zone. Instead, it is a
description of the date, as used for birthdays. It cannot represent
an instant on the time-line without additional information such as
an offset or time-zone.

Instances of this class are immutable and not affected by any
method calls.


## Constants


### ISO8601

```php
final public const ISO8601 = 'Y-m-d';
```


### RFC2822

```php
final public const RFC2822 = 'D, d M Y';
```


### RFC3339

```php
final public const RFC3339 = 'Y-m-d';
```


### SQL

```php
final public const SQL = 'Y-m-d';
```


## Static Methods


### epoch

```php
public static function epoch(): static;
```

The Unix epoch (1 January 1970).


### now

```php
public static function now(
   TimeZone|Offset|string $timeZone = 'UTC',
): static;
```

Obtains the current date from the system clock in the specified
time-zone. If no time-zone is specified, the `UTC` time-zone
will be used.

**Throws:**

`TimeZoneException` if the time-zone name cannot be found


### of

```php
public static function of(
   int $year = 1970,
   int $month = 1,
   int $day = 1,
): static;
```

Makes a new `LocalDate` with the specified year, month and
day-of-month. The day must be valid for the given year and
month, otherwise an exception will be thrown.

All parameters are optional and, if not specified, will take
their Unix epoch value (1 January 1970).

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

**Throws:**

`OutOfRangeException` if the value of any unit is out of range


### fromDayOfYear

```php
public static function fromDayOfYear(
   int $year = 1970,
   int $day = 1,
): static;
```

Makes a new `LocalDate` from a year and day-of-year. The day
must be valid for the given year, otherwise an exception will
be thrown.

Both parameters are optional and, if not specified, will take
their Unix epoch value (1st of 1970).

**Parameters:**

`$year` the year

`$day` the day of the year, from 1 to 366

**Throws:**

`OutOfRangeException` if the value of any unit is out of range


### parse

```php
public static function parse(
   string $string,
   string|array $format = LocalDate::ISO8601,
): Ok|Error;
```

Makes a new `LocalDate` from a text string using a specific
format. It also accepts a list of formats.

If the format is not specified, the ISO 8601 date format will
be used (`Y-m-d`).

The `LocalDate` is not returned directly, but a [result][php-results-doc]
that will contain the date if no error was found, or an exception if
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
public static function fromIso8601(string $value): static;
```

Makes a new `LocalDate` from a text with the ISO 8601 date
format (e.g. `'2023-02-17'`).

The date is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromRfc2822

```php
public static function fromRfc2822(string $value): static;
```

Makes a new `LocalDate` from a text with the RFC 2822 date
format (e.g. `'Fri, 17 Feb 2023'`).

The date is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromRfc3339

```php
public static function fromRfc3339(string $value): static;
```

Makes a new `LocalDate` from a text with the RFC 3339 date
format (e.g. `'2023-02-17'`).

The date is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromSql

```php
public static function fromSql(string $value): static;
```

Makes a new `LocalDate` from a text with the SQL date format
(e.g. `'2023-02-17'`).

The date is returned directly if no error is found, otherwise
an exception is thrown.

**Throws:**

`ParseException` if the text cannot be parsed


### fromNative

```php
public static function fromNative(
   NativeDateTimeInterface $value
): static;
```

Makes a new `LocalDate` from a native `DateTime` or
`DateTimeImmutable`.

Only the date values will be taken, while time and time-zone
values will be ignored.


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this date as a `string`, using the default format of
the class.


### format

```php
public function format(string $format = LocalDate::ISO8601): Ok|Error;
```

Formats this date using the specified format.

If the format is not specified, the ISO 8601 date format will
be used (`Y-m-d`).

The text is not returned directly, but a [result][php-results-doc]
that will contain the text if no error was found, or an exception if
something went wrong.

**Returns:**

`Ok<string>` if no error was found

`Error<FormatException>` if something went wrong


### toIso8601

```php
public function toIso8601(): string;
```

Formats this date with the ISO 8601 date format (e.g.
`'2023-02-17'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc2822

```php
public function toRfc2822(): string;
```

Formats this date with the RFC 2822 date format (e.g.
`'Fri, 17 Feb 2023'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toRfc3339

```php
public function toRfc3339(): string;
```

Formats this date with the RFC 3339 date format (e.g.
`'2023-02-17'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toSql

```php
public function toSql(): string;
```

Formats this date with the SQL date format (e.g.
`'2023-02-17'`).

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toNative

```php
public function toNative(): NativeDateTime;
```

Returns a native `DateTimeImmutable` with the values of this
date.

The time and time-zone values will be taken from the Unix epoch
(00:00:00 UTC).


### atTime

```php
public function atTime(
   LocalTime|int $hour = 0,
   int $minute = 0,
   int $second = 0,
   int $microsecond = 0,
): LocalDateTime;
```

Combines this date with a time to make a `LocalDateTime`. It
accepts a `LocalTime` or individual time units.

If a `LocalTime` is passed as the first argument, no further
arguments will be accepted.

If individual time units are passed, they must be within their
valid range. Missing units will be taken from the Unix epoch
(00:00:00).

**Throws:**

`InvalidArgumentException` if a `LocalTime` is combined with some time units

`OutOfRangeException` if the value of any unit is out of range


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


### compareTo

```php
public function compareTo(LocalDate $that): int;
```

Compares this date to another date.

Returns a negative integer, zero, or a positive integer as this
date is before, equal to, or after the given date.


### is

```php
public function is(LocalDate $that): bool;
```

Checks if the given date belongs to the same class and has the
same value as this date.


### isNot

```php
public function isNot(LocalDate $that): bool;
```

Checks if the given date belongs to another class and has a
different value than this date.


### isEqual

```php
public function isEqual(LocalDate $that): bool;
```

Checks if the given date has the same value as this date.


### isNotEqual

```php
public function isNotEqual(LocalDate $that): bool;
```

Checks if the given date has a different value from this date.


### isGreater

```php
public function isGreater(LocalDate $that): bool;
```

Checks if this date is after the specified date.


### isGreaterOrEqual

```php
public function isGreaterOrEqual(LocalDate $that): bool;
```

Checks if this date is after or equal to the specified date.


### isLess

```php
public function isLess(LocalDate $that): bool;
```

Checks if this date is before the specified date.


### isLessOrEqual

```php
public function isLessOrEqual(LocalDate $that): bool;
```

Checks if this date is before or equal to the specified date.


### plus

```php
public function plus(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
): static;
```

Returns a copy of this date with the specified amount of years,
months and days added. It accepts a `Period` or individual time
units.

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
specify overflow and units other than years, months and days,
since only the order of the three first parameters is
guaranteed.

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### minus

```php
public function minus(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
): static;
```

Returns a copy of this date with the specified amount of years,
months and days subtracted. It accepts a `Period` or individual
time units.

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
specify overflow and units other than years, months and days,
since only the order of the three first parameters is
guaranteed.

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### with

```php
public function with(
   ?int $year = null,
   ?int $month = null,
   ?int $day = null,
): static;
```

Returns a copy of this date with the specified year, month and
day.

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

**Throws:**

`OutOfRangeException` if the value of any unit is out of range


### add

```php
public function add(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
): Ok|Error;
```

Makes a copy of this date with the specified amount of years,
months and days added. It works the same as the [plus()](#plus)
method, but returns a [result][php-results-doc] instead of the new
date.

The result will contain the new date if no error was found, or
an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Returns:**

`Ok<static>` if no error was found

`Error<ArithmeticError|OutOfRangeException>` if something went wrong

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### subtract

```php
public function subtract(
   int|Period $years = 0,
   int $months = 0,
   int $days = 0,
   bool $overflow = false,
   int $millennia = 0,
   int $centuries = 0,
   int $decades = 0,
   int $quarters = 0,
   int $weeks = 0,
):  Ok|Error;
```

Makes a copy of this date with the specified amount of years,
months and days subtracted. It works the same as the
[minus()](#minus) method, but returns a [result][php-results-doc]
instead of the new date.

The result will contain the new date if no error was found, or
an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Returns:**

`Ok<static>` if no error was found

`Error<ArithmeticError|OutOfRangeException>` if something went wrong

**Throws:**

`InvalidArgumentException` if a `Period` is combined with some time units


### copy

```php
public function copy(
   ?int $year = null,
   ?int $month = null,
   ?int $day = null,
): Ok|Error;
```

Makes a copy of this date with the specified year, month and
day. It works the same as the [with()](#with) method, but returns
a [result][php-results-doc] instead of the new date.

The result will contain the new date if no error was found, or
an exception if something went wrong.

**Parameters:**

`$year` the year

`$month` the month of the year, from 1 to 12

`$day` the day of the month, from 1 to 31

**Returns:**

`Ok<static>` if no error was found

`Error<OutOfRangeException>` if something went wrong


[php-results-doc]: https://hereldar.github.io/php-results/
