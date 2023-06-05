<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Period;

use Hereldar\DateTimes\Period;
use Hereldar\DateTimes\Tests\TestCase;

final class SignTest extends TestCase
{
    public function testPositiveValues(): void
    {
        $period = Period::of(1, 2, 3, 4, 5, 6, 7);

        self::assertTrue($period->hasPositiveValues());
        self::assertFalse($period->hasNegativeValues());
        self::assertTrue($period->isPositive());
        self::assertFalse($period->isNegative());
        self::assertFalse($period->isZero());

        self::assertPeriod($period->abs(), 1, 2, 3, 4, 5, 6, 7);
        self::assertPeriod($period->negated(), -1, -2, -3, -4, -5, -6, -7);
    }

    public function testMixedSigns(): void
    {
        $period = Period::of(1, -2, 3, -4, 5, -6, 7);

        self::assertTrue($period->hasPositiveValues());
        self::assertTrue($period->hasNegativeValues());
        self::assertFalse($period->isPositive());
        self::assertFalse($period->isNegative());
        self::assertFalse($period->isZero());

        self::assertPeriod($period->abs(), 1, 2, 3, 4, 5, 6, 7);
        self::assertPeriod($period->negated(), -1, 2, -3, 4, -5, 6, -7);
    }

    public function testNegativeValues(): void
    {
        $period = Period::of(-1, -2, -3, -4, -5, -6, -7);

        self::assertFalse($period->hasPositiveValues());
        self::assertTrue($period->hasNegativeValues());
        self::assertFalse($period->isPositive());
        self::assertTrue($period->isNegative());
        self::assertFalse($period->isZero());

        self::assertPeriod($period->abs(), 1, 2, 3, 4, 5, 6, 7);
        self::assertPeriod($period->negated(), 1, 2, 3, 4, 5, 6, 7);
    }

    public function testZeroValues(): void
    {
        $period = Period::of(0, 0, 0, 0, 0, 0, 0);

        self::assertFalse($period->hasPositiveValues());
        self::assertFalse($period->hasNegativeValues());
        self::assertFalse($period->isPositive());
        self::assertFalse($period->isNegative());
        self::assertTrue($period->isZero());

        self::assertPeriod($period->abs(), 0, 0, 0, 0, 0, 0, 0);
        self::assertPeriod($period->negated(), 0, 0, 0, 0, 0, 0, 0);
    }

    public function testZero(): void
    {
        $period = Period::zero();

        self::assertFalse($period->hasPositiveValues());
        self::assertFalse($period->hasNegativeValues());
        self::assertFalse($period->isPositive());
        self::assertFalse($period->isNegative());
        self::assertTrue($period->isZero());

        self::assertPeriod($period->abs(), 0, 0, 0, 0, 0, 0, 0);
        self::assertPeriod($period->negated(), 0, 0, 0, 0, 0, 0, 0);
    }
}
