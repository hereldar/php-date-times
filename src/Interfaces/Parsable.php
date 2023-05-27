<?php

declare(strict_types=1);

namespace Hereldar\DateTimes\Interfaces;

use Hereldar\DateTimes\Exceptions\ParseException;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @internal
 */
interface Parsable
{
    /**
     * @param string|array<int, string> $format
     *
     * @return Ok<static>|Error<ParseException>
     */
    public static function parse(
        string $string,
        string|array $format,
    ): Ok|Error;
}
