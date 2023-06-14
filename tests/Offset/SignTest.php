<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class SignTest extends TestCase
{
    public function testPositiveValues(): void
    {
        $offset = Offset::of(1, 2, 3);

        self::assertTrue($offset->isPositive());
        self::assertFalse($offset->isNegative());
        self::assertFalse($offset->isZero());
    }

    public function testMixedSigns(): void
    {
        $offset = Offset::of(1, -2, 3);

        self::assertTrue($offset->isPositive());
        self::assertFalse($offset->isNegative());
        self::assertFalse($offset->isZero());
    }

    public function testNegativeValues(): void
    {
        $offset = Offset::of(-1, -2, -3);

        self::assertFalse($offset->isPositive());
        self::assertTrue($offset->isNegative());
        self::assertFalse($offset->isZero());
    }

    public function testZeroValues(): void
    {
        $offset = Offset::of(0, 0, 0);

        self::assertFalse($offset->isPositive());
        self::assertFalse($offset->isNegative());
        self::assertTrue($offset->isZero());
    }

    public function testZero(): void
    {
        $offset = Offset::zero();

        self::assertFalse($offset->isPositive());
        self::assertFalse($offset->isNegative());
        self::assertTrue($offset->isZero());
    }
}
