<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\Offset;

use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\Tests\TestCase;

final class CopyTest extends TestCase
{
    public function testHours(): void
    {
        $one = Offset::of(hours: 10);
        $two = $one->with(hours: 3);
        self::assertOffset($one, 10, 0, 0);
        self::assertOffset($two, 3, 0, 0);
    }

    public function testMinutes(): void
    {
        $one = Offset::of(minutes: 10);
        $two = $one->with(minutes: 3);
        self::assertOffset($one, 0, 10, 0);
        self::assertOffset($two, 0, 3, 0);
    }

    public function testSeconds(): void
    {
        $one = Offset::of(seconds: 10);
        $two = $one->with(seconds: 3);
        self::assertOffset($one, 0, 0, 10);
        self::assertOffset($two, 0, 0, 3);
    }

    public function testAll(): void
    {
        $one = Offset::parse('10:10:10', '%h:%i:%s')->orFail();
        $two = $one->with(3, 3, 3);
        self::assertOffset($one, 10, 10, 10);
        self::assertOffset($two, 3, 3, 3);
    }
}
