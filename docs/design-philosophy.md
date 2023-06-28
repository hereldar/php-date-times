Design Philosophy
=================

Separation of Concepts
----------------------

`Hereldar\DateTimes` is designed to be type-safe. Thus, there are
separate classes for the distinct concepts of date, time and
date-time:

- `LocalDate` stores a date without a time, or any reference to an
  offset or time-zone.

- `LocalTime` stores a time without a date, or any reference to an
  offset or time-zone.

- `LocalDateTime` stores combines date and time, but still without
  any offset or time-zone.

- `DateTime` stores a "full" date-time with time-zone and resolved
  offset from UTC/Greenwich.

Use `LocalDate`, `LocalTime` and `LocalDateTime` where possible to
better model the domain. For example, a birthday should be stored in a
`LocalDate`. Bear in mind that any use of a time-zone adds
considerable complexity to a calculation.

Immutability
------------

All classes are immutable, so the state of their instances will not be
modified by any method call.

Instead of using setters to modify the instance, you will have to use
the `with()` method to make copies with the desired changes.

The same applies for arithmetic operations, such as `plus()` or
`minus()`. They will also not modify the instance, so you will have to
store the result.

Type Safety
-----------

Parameters and return values are totally typed, providing static
analysis tools with all the information they need to check your code.

This also allows IDEs to provide useful hints, helping to find
type-related bugs at development time.

Cohesion
--------

`Hereldar\DateTimes` provides several classes with a relatively high
number of methods. This is made manageable through the use of
consistent method names and prefixes:

- `of` for the main static factory method.
- `parse` for a static factory method focused on parsing text.
- `from` for other static factory methods, such as `fromIso8601()`.
- `is` to check if something is true, such as `isEqual()`.
- `to` to convert this object to another type, such as `toNative()`.
- `at` to combine this object with another, such as `Date::atTime()`.

Getters do not have a `get` prefix. Instead, they have the name of the
fetched field, such as `Time::hour()`.

Verbs are used for methods that return a [result][php-results-doc],
such as `add()` or `subtract()`. The result will contain the expected
value if no error was found, or an exception if something went wrong.

All these methods have a traditional counterpart which returns the
expected value directly. However, methods that return a result could
be useful to do not forget to handle errors.

To reduce the number of methods, the time units are combined into a
single method. Thus, instead of having `addYears()`, `addMonths()` and
so on, the `add()` method has one parameter for each supported time
unit.

These parameters are optional, defaulting to zero or to the Unix epoch,
so named arguments can be used to specify only the units needed.


[php-results-doc]: https://hereldar.github.io/php-results/
