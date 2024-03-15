
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
- Type safety
- Cohesion

Installation
------------

Via Composer:

```bash
composer require hereldar/date-times
```

Development
-----------

Run the following commands from the project folder:

```bash
make tests
make static-analysis
make coding-standards
```

To execute:

- A [PHPUnit](https://phpunit.de) test suite.
- [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/) for
  static code analysis.
- [Easy Coding Standard](https://github.com/easy-coding-standard/easy-coding-standard)
  to fix coding standards.
