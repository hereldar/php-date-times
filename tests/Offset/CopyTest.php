<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;
use OutOfRangeException;

final class CopyTest extends TestCase
{
    public function testHours(): void
    {
        $one = Offset::of(hours: 1);
        $two = $one->with(hours: 2);
        $three = $one->copy(hours: 3)->orFail();
        self::assertOffset($one, 1, 0, 0);
        self::assertOffset($two, 2, 0, 0);
        self::assertOffset($three, 3, 0, 0);

        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, 16 given'),
            fn () => $one->with(hours: 16)
        );
        self::assertException(
            new OutOfRangeException('hours must be between -15 and 15, -16 given'),
            fn () => $one->copy(hours: -16)->orFail()
        );
    }

    public function testMinutes(): void
    {
        $one = Offset::of(minutes: 1);
        $two = $one->with(minutes: 2);
        $three = $one->copy(minutes: 3)->orFail();
        self::assertOffset($one, 0, 1, 0);
        self::assertOffset($two, 0, 2, 0);
        self::assertOffset($three, 0, 3, 0);

        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, 60 given'),
            fn () => $one->with(minutes: 60)
        );
        self::assertException(
            new OutOfRangeException('minutes must be between -59 and 59, -60 given'),
            fn () => $one->copy(minutes: -60)->orFail()
        );
    }

    public function testSeconds(): void
    {
        $one = Offset::of(seconds: 1);
        $two = $one->with(seconds: 2);
        $three = $one->copy(seconds: 3)->orFail();
        self::assertOffset($one, 0, 0, 1);
        self::assertOffset($two, 0, 0, 2);
        self::assertOffset($three, 0, 0, 3);

        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, 60 given'),
            fn () => $one->with(seconds: 60)
        );
        self::assertException(
            new OutOfRangeException('seconds must be between -59 and 59, -60 given'),
            fn () => $one->copy(seconds: -60)->orFail()
        );
    }

    public function testAll(): void
    {
        $one = Offset::parse('1:1:1', '%h:%i:%s')->orFail();
        $two = $one->with(3, 3, 2);
        $three = $one->copy(3, 3, 3)->orFail();
        self::assertOffset($one, 1, 1, 1);
        self::assertOffset($two, 3, 3, 2);
        self::assertOffset($three, 3, 3, 3);

        self::assertException(
            new OutOfRangeException('seconds must be between -54000 and 54000, 54001 given'),
            fn () => $one->with(15, 0, 1)
        );
        self::assertException(
            new OutOfRangeException('seconds must be between -54000 and 54000, -54001 given'),
            fn () => $one->copy(-15, -0, -1)->orFail()
        );
    }
}
