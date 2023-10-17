DateTimes
=========

[![PHP][php-badge]][php-url]
[![Code Coverage][codecov-badge]][codecov-url]
[![Type Coverage][shepherd-coverage-badge]][shepherd-url]
[![Psalm Level][shepherd-level-badge]][shepherd-url]
[![Packagist][packagist-version-badge]][packagist-url]
[![License][license-badge]][license-url]

[php-badge]: https://img.shields.io/badge/php-8.1%20to%208.3-777bb3.svg
[php-url]: https://coveralls.io/github/hereldar/php-date-times
[codecov-badge]: https://img.shields.io/codecov/c/github/hereldar/php-date-times
[codecov-url]: https://app.codecov.io/gh/hereldar/php-date-times
[coveralls-badge]: https://img.shields.io/coverallsCoverage/github/hereldar/php-date-times
[coveralls-url]: https://coveralls.io/github/hereldar/php-date-times
[shepherd-coverage-badge]: https://shepherd.dev/github/hereldar/php-date-times/coverage.svg
[shepherd-level-badge]: https://shepherd.dev/github/hereldar/php-date-times/level.svg
[shepherd-url]: https://shepherd.dev/github/hereldar/php-date-times
[packagist-version-badge]: https://img.shields.io/packagist/v/hereldar/date-times.svg
[packagist-downloads-badge]: https://img.shields.io/packagist/dt/hereldar/date-times.svg
[packagist-url]: https://packagist.org/packages/hereldar/date-times
[license-badge]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[license-url]: LICENSE

This library includes several classes representing the main date-time
concepts, including dates, times, time-zones and periods.

```php
LocalDate::now()->minus(weeks: 1)->atTime(LocalTime::noon())->toIso8601();
```

Highlights
----------

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

Documentation
-------------

- [Guide](https://hereldar.github.io/php-date-times/)
- [Reference](https://hereldar.github.io/php-date-times/reference/)

Credits
-------

- [Samuel Maudo](https://github.com/samuelmaudo)

License
-------

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
