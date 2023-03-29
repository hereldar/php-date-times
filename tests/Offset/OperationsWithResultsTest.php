<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use ArithmeticError;
use DivisionByZeroError;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use InvalidArgumentException;
use OutOfRangeException;

final class OperationsWithResultsTest extends TestCase
{
    public function testAddition(): void
    {
        $offset = Offset::of(hours: 3)->add(hours: 4)->orFail();
        self::assertOffset($offset, 7);

        $offset = Offset::of(hours: 3)->add(Offset::of(hours: 4))->orFail();
        self::assertOffset($offset, 7);

        $offset = Offset::of(hours: 3)->add(hours: -4)->orFail();
        self::assertOffset($offset, -1);

        $offset = Offset::of(hours: 3)->add(Offset::of(hours: -4))->orFail();
        self::assertOffset($offset, -1);

        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(18)->add(Offset::of(1))->orFail()
        );

        self::assertException(
            InvalidArgumentException::class,
            fn () => Offset::zero()->add(Offset::of(1), 2)
        );
    }

    public function testSubtraction(): void
    {
        $offset = Offset::of(hours: 3)->subtract(hours: 4)->orFail();
        self::assertOffset($offset, -1);

        $offset = Offset::of(hours: 3)->subtract(Offset::of(hours: 4))->orFail();
        self::assertOffset($offset, -1);

        $offset = Offset::of(hours: 3)->subtract(hours: -4)->orFail();
        self::assertOffset($offset, 7);

        $offset = Offset::of(hours: 3)->subtract(Offset::of(hours: -4))->orFail();
        self::assertOffset($offset, 7);

        self::assertException(
            OutOfRangeException::class,
            fn () => Offset::of(-18)->subtract(Offset::of(1))->orFail()
        );

        self::assertException(
            InvalidArgumentException::class,
            fn () => Offset::zero()->subtract(Offset::of(1), 2)
        );
    }
}
