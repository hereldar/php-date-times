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
interface Summable
{
    public function plus(): static;

    public function minus(): static;

    /**
     * @return Ok<static>|Error<ArithmeticError>|Error<OutOfRangeException>
     */
    public function add(): Ok|Error;

    /**
     * @return Ok<static>|Error<ArithmeticError>|Error<OutOfRangeException>
     */
    public function subtract(): Ok|Error;
}
