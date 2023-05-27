<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Enums;

enum TimeZoneType: int
{
    /** UTC offset (-06:00) */
    case Offset = 1;

    /** Time-zone abbreviation (BST) */
    case Abbreviation = 2;

    /** Time-zone identifier (Australia/Hobart) */
    case Identifier = 3;
}
