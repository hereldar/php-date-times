<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

/**
 * @internal
 */
interface Datelike
{
    public function year(): int;

    /**
     * @return int<1, 12>
     */
    public function month(): int;

    public function week(): int;

    public function weekYear(): int;

    /**
     * Returns the day of month.
     *
     * @return int<1, 31>
     */
    public function day(): int;

    /**
     * @return int<1, 7>
     */
    public function dayOfWeek(): int;

    /**
     * @return int<1, 366>
     */
    public function dayOfYear(): int;

    public function inLeapYear(): bool;
}
