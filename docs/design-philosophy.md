Design Philosophy
=================

Separation of Concepts
----------------------

`Hereldar\DateTimes` is designed to be type-safe. Thus, there are
separate classes for the distinct concepts of date, time and
date-time:

- [`LocalDate`](reference/local-date) stores a date without a time,
  or any reference to an offset or time-zone.

- [`LocalTime`](reference/local-time) stores a time without a date,
  or any reference to an offset or time-zone.

- [`LocalDateTime`](reference/local-date-time) stores combines date
  and time, but still without any offset or time-zone.

- [`DateTime`](reference/date-time) stores a "full" date-time with
  time-zone and resolved offset from UTC/Greenwich.

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

In return, you will be able to pass instances to any function with the
guarantee that the passed instances will remain unchanged.

Type Safety
-----------

In addition to having a different type for each concept, the
parameters and return values are totally typed, providing static
analysis tools with all the information they need to check your code.

This also allows IDEs to provide useful hints, helping you find
type-related bugs at development time.

Cohesion
--------

This library provides several classes with a relatively large number
of methods. To make it manageable, methods are named in a consistent
way:

- `of` for the main static factory method.
- `parse` for a static factory method focused on parsing text.
- `from` for other static factory methods, such as `fromIso8601()`.
- `is` to check if something is true, such as `isEqual()`.
- `to` to convert this object to another type, such as `toNative()`.
- `at` to combine this object with another, such as `LocalDate::atTime()`

Getters do not have a `get` prefix. Instead, they have the name of the
fetched field, such as `LocalTime::hour()`.

Verbs are used for methods that return a [result][php-results-doc],
such as `add()` or `subtract()`. The result will contain the expected
value if no error was found, or an exception if something went wrong.

All these methods have a traditional counterpart which returns the
expected value directly. However, methods that return a result could
be useful to do not forget to handle errors.

To reduce the number of methods, several time units are combined in a
single method. Thus, instead of having `addYears()`, `addMonths()` and
so on, the `add()` method has one parameter for each supported time
unit.


[native-date-time]: https://www.php.net/manual/en/class.datetime.php
[native-date-time-immutable]: https://www.php.net/manual/en/class.datetimeimmutable.php
[native-date-time-zone]: https://www.php.net/manual/en/class.datetimezone.php
[native-date-interval]: https://www.php.net/manual/en/class.dateinterval.php
[php-results-doc]: https://hereldar.github.io/php-results/
