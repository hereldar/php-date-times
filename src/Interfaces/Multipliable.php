<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Multipliable
{
    public function multipliedBy(int $multiplicand): static;

    public function dividedBy(int $divisor): static;

    public function multiplyBy(int $multiplicand): Ok|Error;

    public function divideBy(int $divisor): Ok|Error;
}
