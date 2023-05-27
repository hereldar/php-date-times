<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

/**
 * @internal
 */
interface Timelike
{
    /**
     * @return int<0, 23>
     */
    public function hour(): int;

    /**
     * @return int<0, 59>
     */
    public function minute(): int;

    /**
     * @return int<0, 59>
     */
    public function second(): int;

    /**
     * @return int<0, 999>
     */
    public function millisecond(): int;

    /**
     * @return int<0, 999999>
     */
    public function microsecond(): int;
}
