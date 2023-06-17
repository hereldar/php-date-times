<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Copyable
{
    public function with(): static;

    public function copy(): Ok|Error;
}
