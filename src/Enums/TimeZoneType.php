<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Enums;

enum TimeZoneType: int
{
    /** Fixed offset (-06:00) */
    case Offset = 1;

    /** Time-zone identifier (Australia/Hobart) */
    case Identifier = 3;

    /** Time-zone abbreviation (BST) */
    case Abbreviation = 2;
}
