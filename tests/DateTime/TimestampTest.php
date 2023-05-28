<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\DateTime;

use Hereldar\DateTimes\DateTime;
use Hereldar\DateTimes\Tests\TestCase;

final class TimestampTest extends TestCase
{
    public function testSecondsSinceEpoch(): void
    {
        $seconds = DateTime::of(1986, 12, 25, 12, 30, 25, 999)->secondsSinceEpoch();
        $dateTime = DateTime::fromSecondsSinceEpoch($seconds);
        self::assertDateTime($dateTime, 1986, 12, 25, 12, 30, 25, 0);
    }

    public function testMicrosecondsSinceEpoch(): void
    {
        [$seconds, $microseconds] = DateTime::of(1986, 12, 25, 12, 30, 25, 999)->microsecondsSinceEpoch();
        $dateTime = DateTime::fromMicrosecondsSinceEpoch($seconds, $microseconds);
        self::assertDateTime($dateTime, 1986, 12, 25, 12, 30, 25, 999);
    }
}
