<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeZone as NativeTimeZone;
use Hereldar\DateTimes\Enums\TimeZoneType;

interface ITimeZone
{
    public function toNative(): NativeTimeZone;

    public function name(): string;

    public function type(): TimeZoneType;

    public function toOffset(ILocalDate|ILocalDateTime $date): IOffset;

    public function compareTo(ITimeZone $that): int;

    public function is(self $that): bool;

    public function isNot(self $that): bool;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;
}
