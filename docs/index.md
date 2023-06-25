
Getting Started
===============

`Hereldar\DateTimes` is a library with several classes representing
the main date-time concepts, including dates, times, time-zones and
periods.

```php
LocalDate::now()->minus(weeks: 1)->atTime(LocalTime::noon())->toIso8601();
```

Highlights
--------

- Separation of concepts
- Immutability
- Strong typing

Installation
------------

Via Composer:

```bash
composer require hereldar/date-times
```

Testing
-------

Run the following command from the project folder:

```bash
composer test
```

To execute:

- A [PHPUnit](https://phpunit.de) test suite.
- [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/) for
  static code analysis.
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) to fix
  coding standards.
