<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use DateTimeZone as StandardTimeZone;
use Stringable;

interface ITimeZone extends Stringable
{
    public function toStandard(): StandardTimeZone;

    public function name(): string;

    public function offset(ILocalDate|ILocalDateTime $date): IOffset;

    public function is(self $that): bool;

    public function isNot(self $that): bool;

    public function isEqual(self $that): bool;

    public function isNotEqual(self $that): bool;
}
