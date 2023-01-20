<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeZone as StandardTimeZone;
use Stringable;

interface IOffset extends Stringable
{
    public function toStandardTimeZone(): StandardTimeZone;

    final public const ISO8601 = '%R%H:%I'; // +02:00

    public function format(string $format = self::ISO8601): string;

    public function toIso8601(): string;

    public function hours(): int;

    public function minutes(): int;

    public function totalMinutes(): int;

    public function totalSeconds(): int;

    public function compareTo(self $that): int;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;

    public function isGreater(self $that): bool;

    public function isGreaterOrEqual(self $that): bool;

    public function isLess(self $that): bool;

    public function isLessOrEqual(self $that): bool;

    public function isNegative(): bool;

    public function isZero(): bool;
}
