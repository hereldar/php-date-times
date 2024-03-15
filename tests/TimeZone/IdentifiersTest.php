<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use Hereldar\DateTimes\Enums\TimeZoneGroup;
use Hereldar\DateTimes\Exceptions\CountryException;
use Hereldar\DateTimes\Tests\TestCase;
use Hereldar\DateTimes\TimeZone;

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
