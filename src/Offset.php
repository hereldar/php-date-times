<?php

declare(strict_types=1);

namespace Hereldar\DateTimes;

use DateTimeZone as StandardTimeZone;
use Hereldar\DateTimes\Interfaces\IOffset;

class Offset implements IOffset
{
    public function __construct(
        private readonly int $value
    ) {
    }

    public function __toString(): string
    {
        return $this->toIso8601();
    }

    public function toStandardTimeZone(): StandardTimeZone
    {
        // TODO: Implement toStandardTimeZone() method.
    }

    public function format(string $format = self::ISO8601): string
    {
        // TODO: Implement format() method.
    }

    public function toIso8601(): string
    {
        return sprintf(
            '%+02d:%02d',
            $this->hours(),
            abs($this->minutes())
        );
    }

    public function hours(): int
    {
        return intdiv($this->totalMinutes(), 60);
    }

    public function minutes(): int
    {
        return $this->totalMinutes() % 60;
    }

    public function totalMinutes(): int
    {
        return intdiv($this->value, 60);
    }

    public function totalSeconds(): int
    {
        return $this->value;
    }

    public function compareTo(IOffset $that): int
    {
        $a = $this->value;
        $b = $that->totalSeconds();

        return match (true) {
            ($a == $b) => 0,
            ($a > $b) => 1,
            default => -1,
        };
    }

    public function isEqual(IOffset $that): bool
    {
        return ($this->value === $that->totalSeconds());
    }

    public function isNotEqual(IOffset $that): bool
    {
        return ($this->value !== $that->totalSeconds());
    }

    public function isGreater(IOffset $that): bool
    {
        return ($this->value > $that->totalSeconds());
    }

    public function isGreaterOrEqual(IOffset $that): bool
    {
        return ($this->value >= $that->totalSeconds());
    }

    public function isLess(IOffset $that): bool
    {
        return ($this->value < $that->totalSeconds());
    }

    public function isLessOrEqual(IOffset $that): bool
    {
        return ($this->value <= $that->totalSeconds());
    }

    public function isNegative(): bool
    {
        return ($this->value < 0);
    }

    public function isZero(): bool
    {
        return ($this->value === 0);
    }
}
