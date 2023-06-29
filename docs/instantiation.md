Instantiation
=============

The most common way to instantiate objects in PHP is to use the `new`
operator. However, this operator calls the `__construct()` method
directly, creating a dependency between the users of the class and its
internal structure, and preventing it from changing in the future.

For this reason, all `Hereldar\DateTimes` classes have the private
constructor and, instead, several factory methods are provided.

of
--

The main factory method is `of()`. This method receives the components
that the class stores, mainly time units.

For example, when creating a date, the year, month and day can be
specified:

```php
LocalDate::of(2014, 06, 28);
```

Although, if the `of()` method accepts several arguments, they are all
optional, being their default value zero, or their value in the Unix
epoch (00:00:00 UTC on 1 January 1970).

If you only specify the year, the month will be January and the day
will be 1:

```php
LocalDate::of(1986);
```

The `of()` method can also accept other arguments, such as multiples
of the units it stores.

For example, the [`Period`](reference/period) class stores years
(among other units), but the [`Period::of()`](reference/period.md#of)
method also accepts decades, centuries and millennia:

```php
Period::of(centuries: 20);
```

parse
-----

Most classes have a `parse()` factory method, which allows you to
create instances from text. However, it does not return the instance
directly, but a [result][php-results-doc], allowing developers to
handle errors as they see fit.

The default format is the [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)
standard, although other formats can also be specified:

```php
LocalTime::parse('12:34:56')->orFail();
```

Dates and times recognize the same characters as the native class
[`DateTimeImmutable`](https://www.php.net/manual/en/datetimeimmutable.createfromformat.php):

```php
LocalDate::parse('15-Feb-2009', 'j-M-Y')->orNull();
```

Whereas periods use the characters from the native
[`DateInterval`](https://www.php.net/manual/en/dateinterval.format.php)
class with some additions:

```php
Period::parse('01:02:03', '%H:%I:%S')->orFalse();
```

It is planned to implement both systems in all classes, so how to add
the ISO format.

Finally, the `parse()` factory method can receive multiple formats, in
which case it will only return an error if the text is not compatible
with any of them:

```php
LocalDate::parse($input, ['Y-m-d', 'j-M-Y'])->or(LocalDate::now(...));
```

Other Factories
---------------

Many other factory methods are also included, such as `now()`, `zero()`,
`fromRfc3339()` or `fromNative()`.

You can consult the [reference](reference/) to check all the factory
methods of each class.


[native-date-time]: https://www.php.net/manual/en/class.datetime.php
[native-date-time-immutable]: https://www.php.net/manual/en/class.datetimeimmutable.php
[native-date-time-zone]: https://www.php.net/manual/en/class.datetimezone.php
[native-date-interval]: https://www.php.net/manual/en/class.dateinterval.php
[php-results-doc]: https://hereldar.github.io/php-results/
