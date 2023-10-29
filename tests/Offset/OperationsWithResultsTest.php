<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use ArithmeticError;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $offset = Offset::of(hours: 3)->add(hours: 4)->orFail();
        self::assertOffset($offset, 7);

        $offset = Offset::of(hours: 3)->add(hours: -4)->orFail();
        self::assertOffset($offset, -1);

        self::assertException(
            OutOfRangeException::class,
            fn() => Offset::of(Offset::HOURS_MAX)->add(1)->orFail()
        );

        self::assertException(
            ArithmeticError::class,
            fn() => Offset::of(seconds: 1)->add(seconds: PHP_INT_MAX)->orFail()
        );
    }

    public function testSubtraction(): void
    {
        $offset = Offset::of(hours: 3)->subtract(hours: 4)->orFail();
        self::assertOffset($offset, -1);

        $offset = Offset::of(hours: 3)->subtract(hours: -4)->orFail();
        self::assertOffset($offset, 7);

        self::assertException(
            OutOfRangeException::class,
            fn() => Offset::of(Offset::HOURS_MIN)->subtract(1)->orFail()
        );

        self::assertException(
            ArithmeticError::class,
            fn() => Offset::of(seconds: -2)->subtract(seconds: PHP_INT_MAX)->orFail()
        );
    }
}
