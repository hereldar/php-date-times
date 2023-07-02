<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeZone as NativeTimeZone;
use Generator;
use Hereldar\DateTimes\Enums\TimeZoneGroup;
use Hereldar\DateTimes\Enums\TimeZoneType;
use Hereldar\DateTimes\Exceptions\CountryException;
use Hereldar\DateTimes\Exceptions\TimeZoneException;
use Hereldar\DateTimes\TimeZone;
use Hereldar\DateTimes\Tests\TestCase;
use Throwable;

final class IdentifiersTest extends TestCase
{
    public function testGroups(): void
    {
        foreach (TimeZoneGroup::cases() as $group) {
            $identifiers = TimeZone::identifiers($group);
            self::assertIsList($identifiers);
            self::assertNotEmpty($identifiers);
        }
    }

    public function testCountries(): void
    {
        $identifiers = TimeZone::countryIdentifiers('MX');
        self::assertIsList($identifiers);
        self::assertNotEmpty($identifiers);

        self::assertException(
            new CountryException('UK'),
            fn () => TimeZone::countryIdentifiers('UK')
        );

        self::assertException(
            new CountryException('Bad'),
            fn () => TimeZone::countryIdentifiers('Bad')
        );
    }
}
