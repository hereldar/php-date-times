<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\DateTimes\Exceptions\FormatException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Formattable
{
    /**
     * @return Ok<string>|Error<FormatException>
     */
    public function format(string $format): Ok|Error;
}
