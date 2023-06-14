<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use ArithmeticError;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use OutOfRangeException;

/**
 * @internal
 */
interface Multiplicable
{
    public function multipliedBy(int $multiplicand): static;

    public function dividedBy(int $divisor): static;

    /**
     * @return Ok<static>|Error<ArithmeticError|OutOfRangeException>
     */
    public function multiplyBy(int $multiplicand): Ok|Error;

    /**
     * @return Ok<static>|Error<ArithmeticError|OutOfRangeException>
     */
    public function divideBy(int $divisor): Ok|Error;
}
