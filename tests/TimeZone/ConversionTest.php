<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Tests\TimeZone;

use DateTimeImmutable as NativeDateTime;
use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\LocalDate;
use Hereldar\DateTimes\LocalDateTime;
use Hereldar\DateTimes\Offset;
use Hereldar\DateTimes\TimeZone;
use Hereldar\DateTimes\Tests\TestCase;

final class ConversionTest extends TestCase
{
    public function testOffset(): void
    {
        $summer = LocalDate::parse('2020-06-15')->orFail();
        $winter = LocalDateTime::parse('2018-12-20T00:00:00')->orFail();

        self::assertOffset(TimeZone::of('Europe/Paris')->toOffset($summer), 2, 0);
        self::assertOffset(TimeZone::of('Europe/Paris')->toOffset($winter), 1, 0);

        self::assertOffset(TimeZone::of('Asia/Calcutta')->toOffset($summer), 5, 30);
        self::assertOffset(TimeZone::of('Asia/Calcutta')->toOffset($winter), 5, 30);

        self::assertOffset(TimeZone::of('Pacific/Chatham')->toOffset($summer), 12, 45);
        self::assertOffset(TimeZone::of('Pacific/Chatham')->toOffset($winter), 13, 45);

        self::assertOffset(TimeZone::of('Pacific/Marquesas')->toOffset($summer), -9, -30);
        self::assertOffset(TimeZone::of('Pacific/Marquesas')->toOffset($winter), -9, -30);

        self::assertOffset(TimeZone::of('-8:45')->toOffset($summer), -8, -45);
        self::assertOffset(TimeZone::of('-8:45')->toOffset($winter), -8, -45);

        $offset = Offset::of(-5, -15);
        self::assertOffset(TimeZone::fromOffset($offset)->toOffset($summer), $offset->hours(), $offset->minutes());
        self::assertOffset(TimeZone::fromOffset($offset)->toOffset($winter), $offset->hours(), $offset->minutes());

        $offset = Offset::fromTotalMinutes(-150);
        self::assertOffset(TimeZone::fromOffset($offset)->toOffset($summer), $offset->hours(), $offset->minutes());
        self::assertOffset(TimeZone::fromOffset($offset)->toOffset($winter), $offset->hours(), $offset->minutes());
    }

    /**
     * @dataProvider timeZoneNames
     */
    public function testOffsetNow(string $timeZoneName): void
    {
        $tz = new NativeTimeZone($timeZoneName);
        $timeZone = TimeZone::of($timeZoneName);

        $now = new NativeDateTime('now');
        $offset = Offset::fromTotalSeconds($tz->getOffset($now));

        self::assertTrue($timeZone->toOffset()->is($offset));
    }
}
