Design philosophy
=================

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
`LocalDate`. Bear in mind that any use of a time-zone, such as
'America/Mexico_City', adds considerable complexity to a calculation.

The classes have a relatively high number of methods. This is made
manageable through the use of consistent method names and prefixes:

- `of` for the main static factory method.
- `parse` for a static factory method focused on parsing text.
- `from` for other static factory methods, such as `DateTime::fromIso8601()`.
- `is` to check if something is true, such as `Period::isEqual()`.
- `to` to convert this object to another type, such as `TimeZone::toNative()`.
- `at` to combine this object with another, such as `Date::atTime()`.

The getter methods do not have a `get` prefix. Instead, they have the
name of the fetched field, such as `Time::hour()`.

Verbs are used for methods that return a [result][php-results-doc],
such as `DateTime::add()`. The result will contain the expected value
if no error was found, or an exception if something went wrong.

All these methods have a traditional counterpart which returns the
expected value directly. However, methods that return a result could
be useful to do not forget to handle errors. 

To reduce the number of methods, many arguments are optional,
defaulting to zero or to the Unix epoch. Named arguments can be used
to specify only the units needed.

The result is intended to be a flexible library, allowing clear and
secure code to be written: 

```php
$today = LocalDate::now();
if ($customer->birthday()->isEqual($today)) {
  $expiryDate = $today->plus(weeks: 2);
  $mailer->sendBirthdaySpecialOffer($customer, $expiryDate);
}
```


[php-results-doc]: https://hereldar.github.io/php-results/
