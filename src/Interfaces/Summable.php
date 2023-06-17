<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Summable
{
    public function plus(): static;

    public function minus(): static;

    public function add(): Ok|Error;

    public function subtract(): Ok|Error;
}
