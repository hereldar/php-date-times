<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Enums;

use DateTimeZone as NativeTimeZone;

enum TimeZoneGroup: int
{
    /** All time-zones. */
    case All = 2047; //NativeTimeZone::ALL

    /** Africa time-zones. */
    case Africa = 1; //NativeTimeZone::AFRICA

    /** Antarctica time-zones. */
    case Antarctica = 4; //NativeTimeZone::ANTARCTICA

    /** Arctic time-zones. */
    case Arctic = 8; //NativeTimeZone::ARCTIC

    /** Asia time-zones. */
    case Asia = 16; //NativeTimeZone::ASIA

    /** Atlantic time-zones. */
    case Atlantic = 32; //NativeTimeZone::ATLANTIC

    /** Australia time-zones. */
    case Australia = 64; //NativeTimeZone::AUSTRALIA

    /** Europe time-zones. */
    case Europe = 128; //NativeTimeZone::EUROPE

    /** Indian time-zones. */
    case Indian = 256; //NativeTimeZone::INDIAN

    /** Pacific time-zones. */
    case Pacific = 512; //NativeTimeZone::PACIFIC

    /** UTC time-zones. */
    case Utc = 1024; //NativeTimeZone::UTC
}
