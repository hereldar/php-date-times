
# Period


An amount of time in the ISO-8601 calendar system, such as '2
months, 3 days and 12 hours'.

This class models a quantity or amount of time in terms of years,
months, days, hours, minutes, seconds and microseconds.

Each time unit is stored individually, and is retrieved as
specified when creating the period.

Instances of this class are immutable and not affected by any
method calls.


## Constants



### ISO8601

```php
final public const ISO8601 = 'P%yY%mM%dDT%hH%iM%s%fS';
```


## Static Methods


### zero

```php
public static function zero(): static;
```

An empty period (P0S).


### of

```php
public static function of(
    int $years = 0,
    int $months = 0,
    int $days = 0,
    int $hours = 0,
    int $minutes = 0,
    int $seconds = 0,
    int $microseconds = 0,
    int $millennia = 0,
    int $centuries = 0,
    int $decades = 0,
    int $quarters = 0,
    int $weeks = 0,
    int $milliseconds = 0,
): static;
```

Makes a new `Period` with the specified years, months, days,
hours, minutes, seconds and microseconds.

All parameters are optional and, if not specified, will be set
to zero.

No normalization is performed.

WARNING: It is strongly recommended to use named arguments to
specify units other than years, months, days, hours, minutes,
seconds and microseconds, since only the order of the seven
first parameters is guaranteed.

**Exceptions:**

`ArithmeticError` if any value exceeds the PHP limits for an integer


### parse

```php
public static function parse(
    string $string,
    string|array $format = Period::ISO8601,
): Ok|Error;
```

Makes a new `Period` from a text string using a specific format.
It also accepts a list of formats.

If the format is not specified, the ISO 8601 period format will
be used (`P%yY%mM%dDT%hH%iM%s%fS`).

The `Period` is not returned directly, but a [result][php-results-doc]
that will contain the time if no error was found, or an exception if
something went wrong.

**Parameters:**

`$string` the text to parse

`$format` the expected format, or a list of accepted formats

**Return Values:**

`Ok<string>` if no error is found

`Error<ParseException>` if the text cannot be parsed

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

**Exceptions:**

`InvalidArgumentException` if an empty list of formats is passed


### fromIso8601

```php
public static function fromIso8601(string $string): static;
```

Makes a new `Period` from a text with the ISO 8601 period
format (e.g. `'P2DT30M'`).

The period is returned directly if no error is found, otherwise
an exception is thrown.

**Exceptions:**

`ParseException` if the text cannot be parsed

`ArithmeticError` if any value exceeds the PHP limits for an integer


### fromNative

```php
public static function fromNative(
    NativeDateInterval $interval,
): static;
```

Makes a new `Period` from a native `DateInterval`.


## Methods


### __toString

```php
public function __toString(): string;
```

Outputs this period as a `string`, using the default format of
the class.


### format

```php
public function format(string $format = Period::ISO8601): Ok|Error;
```

Formats this period using the specified format.

If the format is not specified, the ISO 8601 period format will
be used (`P%yY%mM%dDT%hH%iM%s%fS`).

The text is not returned directly, but a [result][php-results-doc] that will
contain the text if no error was found, or an exception if
something went wrong.

**Return Values:**

`Ok<string>` if no error is found

`Error<FormatException>` if the format is incorrect


### formatted

```php
public function formatted(string $format = Period::ISO8601): string;
```

Formats this period using the specified format.

If the format is not specified, the ISO 8601 period format will
be used (`P%yY%mM%dDT%hH%iM%s%fS`).

The text is returned directly if no error is found, otherwise
an exception is thrown.

**Exceptions:**

`FormatException` if the format is incorrect


### toIso8601

```php
public function toIso8601(): string;
```

Formats this period with the ISO 8601 period format (e.g.
`'P2DT30M'`).

Units equal to zero are not included in the resulting text.

The text is returned directly if no error is found, otherwise
an exception is thrown.


### toNative

```php
public function toNative(): NativeDateInterval;
```

Returns a native `DateInterval` with the values of this period.


### years

```php
public function years(): int;
```

Returns the amount of years.


### months

```php
public function months(): int;
```

Returns the amount of months.


### days

```php
public function days(): int;
```

Returns the amount of days.


### hours

```php
public function hours(): int;
```

Returns the amount of hours.


### minutes

```php
public function minutes(): int;
```

Returns the amount of minutes.


### seconds

```php
public function seconds(): int;
```

Returns the amount of seconds.


### microseconds

```php
public function microseconds(): int;
```

Returns the amount of microseconds.


### compareTo

```php
public function compareTo(Period $that): int;
```

Compares this period to another period.

Returns a negative integer, zero, or a positive integer as this
period is less than, equal to, or greater than the given period.

Values are [normalized](#normalized) before comparison, so a
period of "15 Months" is considered equal to a period of "1 Year
and 3 Months".


### is

```php
public function is(Period $that): bool;
```

Checks if the given period belongs to the same class and has
the same values as this period.


### isNot

```php
public function isNot(Period $that): bool;
```

Checks if the given period belongs to another class or has
different values than this period.


### isEqual

```php
public function isEqual(Period $that): bool;
```

Checks if the given period has the same values as this period.


### isNotEqual

```php
public function isNotEqual(Period $that): bool;
```

Checks if the given period has values different from those of
this period.


### isSimilar

```php
public function isSimilar(Period $that): bool;
```

Checks if the given period has the same [normalized](#normalized)
values as this period.


### isNotSimilar

```php
public function isNotSimilar(Period $that): bool;
```

Checks if the given period has normalized values different from
those of this period.

Values are [normalized](#normalized) before comparison.


### isGreater

```php
public function isGreater(Period $that): bool;
```

Checks if this period is greater than the specified period.

Values are [normalized](#normalized) before comparison.


### isGreaterOrEqual

```php
public function isGreaterOrEqual(Period $that): bool;
```

Checks if this period is greater than or equal to the specified
period.

Values are [normalized](#normalized) before comparison.


### isLess

```php
public function isLess(Period $that): bool;
```

Checks if this period is less than the specified period.

Values are [normalized](#normalized) before comparison.


### isLessOrEqual

```php
public function isLessOrEqual(Period $that): bool;
```

Checks if this period is less than or equal to the specified
period.

Values are [normalized](#normalized) before comparison.


### hasPositiveValues

```php
public function hasPositiveValues(): bool;
```

Checks if this period has any value greater than zero.


### hasNegativeValues

```php
public function hasNegativeValues(): bool;
```

Checks if this period has any value less than zero.


### isPositive

```php
public function isPositive(): bool;
```

Checks if this period has any value greater than zero, and if
none of its values is less than zero.


### isNegative

```php
public function isNegative(): bool;
```

Checks if this period has any value less than zero, and if none
of its values is greater than zero.


### isZero

```php
public function isZero(): bool;
```

Checks if all values of this period are zero.


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
    int $millennia = 0,
    int $centuries = 0,
    int $decades = 0,
    int $quarters = 0,
    int $weeks = 0,
    int $milliseconds = 0,
): static;
```

Returns a copy of this period with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
added.

WARNING: It is strongly recommended to use named arguments to
specify units other than years, months, days, hours, minutes,
seconds and microseconds, since only the order of the seven
first parameters is guaranteed.

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
    int $millennia = 0,
    int $centuries = 0,
    int $decades = 0,
    int $quarters = 0,
    int $weeks = 0,
    int $milliseconds = 0,
): static;
```

Returns a copy of this period with the specified amount of
years, months, days, hours, minutes, seconds and microseconds
subtracted.

WARNING: It is strongly recommended to use named arguments to
specify units other than years, months, days, hours, minutes,
seconds and microseconds, since only the order of the seven
first parameters is guaranteed.

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units

`ArithmeticError` if any value exceeds the PHP limits for an integer


### multipliedBy

```php
public function multipliedBy(int $multiplicand): static;
```

Returns a copy of this period with each of its amounts
multiplied by the specified number.

**Exceptions:**

`ArithmeticError` if any value exceeds the PHP limits for an integer


### dividedBy

```php
public function dividedBy(int $divisor): static;
```

Returns a copy of this period with each of its amounts
divided by the specified number. The remainder of each
division is carried to the next unit.

This is an unsafe operation, since the relationships between
some units are not exact. The number of days in a month varies
from 28 to 31, and some days do not have 24 hours due daylight
saving time. However, this operation considers that months have
30 days and days have 24 hours.


### abs

```php
public function abs(): static;
```

Returns a copy of this period with positive amounts.


### negated

```php
public function negated(): static;
```

Returns a copy of this period with each of its amounts negated.


### normalized

```php
public function normalized(): static;
```

Returns a copy of this period with each of its amounts
divided by the specified number.

This is an unsafe operation, since the relationships between
some units are not exact. The number of days in a month varies
from 28 to 31, and some days do not have 24 hours due daylight
saving time. However, this operation considers that months have
30 days and days have 24 hours.


### with

```php
public function with(
    ?int $years = null,
    ?int $months = null,
    ?int $days = null,
    ?int $hours = null,
    ?int $minutes = null,
    ?int $seconds = null,
    ?int $microseconds = null,
): static;
```

Returns a copy of this period with the specified years, months,
days, hours, minutes, seconds and microseconds.

**Exceptions:**

`ArithmeticError` if any value exceeds the PHP limits for an integer


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
    int $millennia = 0,
    int $centuries = 0,
    int $decades = 0,
    int $quarters = 0,
    int $weeks = 0,
    int $milliseconds = 0,
): Ok|Error;
```

Makes a copy of this period with the specified amount of years,
months, days, hours, minutes, seconds and microseconds added.

It works the same as the [plus()](#plus) method, but returns a
[result][php-results-doc] instead of the new period.

The result will contain the new period if no error was found,
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
    int $millennia = 0,
    int $centuries = 0,
    int $decades = 0,
    int $quarters = 0,
    int $weeks = 0,
    int $milliseconds = 0,
): Ok|Error;
```

Makes a copy of this period with the specified amount of years,
months, days, hours, minutes, seconds and microseconds
subtracted.

It works the same as the [minus()](#minus) method, but returns
a [result][php-results-doc] instead of the new period.

The result will contain the new period if no error was found,
or an exception if something went wrong.

However, if a `Period` is combined with any time unit, the
exception will not be captured, allowing it to be thrown
normally.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer

**Exceptions:**

`InvalidArgumentException` if a `Period` is combined with some time units


### multiplyBy

```php
public function multiplyBy(int $multiplicand): Ok|Error;
```

Makes a copy of this period with each of its amounts multiplied
by the specified number.

It works the same as the [multipliedBy()](#multipliedBy) method, but
returns a [result][php-results-doc] instead of the new period.

The result will contain the new period if no error was found,
or an exception if something went wrong.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer


### divideBy

```php
public function divideBy(int $divisor): Ok|Error;
```

Makes a copy of this period with each of its amounts divided by
the specified number.

It works the same as the [dividedBy()](#dividedBy) method, but
returns a [result][php-results-doc] instead of the new period.

The result will contain the new period if no error was found,
or an exception if something went wrong.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer


### copy

```php
public function copy(
    ?int $years = null,
    ?int $months = null,
    ?int $days = null,
    ?int $hours = null,
    ?int $minutes = null,
    ?int $seconds = null,
    ?int $microseconds = null,
): Ok|Error;
```

Makes a copy of this period with the specified years, months,
days, hours, minutes, seconds and microseconds.

It works the same as the [with()](#with) method, but returns a
[result][php-results-doc] instead of the new period.

The result will contain the new period if no error was found,
or an exception if something went wrong.

**Return Values:**

`Ok<static>` if no error is found

`Error<ArithmeticError>` if any value exceeds the PHP limits for an integer


[php-results-doc]: https://hereldar.github.io/php-results/
